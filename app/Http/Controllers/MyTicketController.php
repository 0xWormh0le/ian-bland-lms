<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ticket;
use App\TicketResponse;
use App\TicketAttachment;
use App\TicketHistory;
use Yajra\Datatables\Datatables;
use Alert, Auth;
use Illuminate\Support\Facades\Storage;
use App\Events\NewTicketNotification;
use App\Events\NewMessageFromUserNotification;

class MyTicketController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      if(validate_role('tickets.myticket'))
      {
        $tickets = Ticket::where('created_by', \Auth::id())
                          ->orderBy('updated_at', 'DESC')->get();
        return view('tickets.my-tickets.index', compact('tickets'));
      }
      else{
        Alert::error(__('messages.unauthorized'));
        return redirect('/');
      }
    }


    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Ticket::find(decrypt($id));
        if($data && $data->created_by == \Auth::id())
        {
           if(!$data->read_by_user)
            {
                $data->read_by_user = true;
                $data->save();
            }

            $breadcrumbs = [
                route('my-tickets.index') => trans('controllers.my_tickets'),
                '' => $data->ticket_number
            ];
            $title =  trans('controllers.details_of_ticket') .' #'.$data->ticket_number;
            return view('tickets.my-tickets.details', compact('title', 'breadcrumbs', 'data'));
        }
        Alert::error(__('messages.invalid_request'));
        return redirect()->route('my-tickets.index');
    }


    public function response(Request $request, $id)
    {
        $response = new TicketResponse;
        $response->ticket_id = decrypt($id);
        $response->responder_id = null;
        $response->content = $request->content;
        $response->save();

        $ticket = Ticket::find($response->ticket_id);

        if(Auth::user()->isClientAdmin())
        {
          $ticket->read_by_admin = false;
        }
        else
          $ticket->read_by_client_admin = false;

        $ticket->read_by_user = true;
        $ticket->save();

        $message = [
            'id' => $ticket->ticket_number,
            'sender' => $ticket->sender_name,
            'assigned_to' => @$ticket->assigned_to ?: 0,
            'message' => get_words($response->content, 10),
            'datetime' => datetime_format($response->created_at, 'j M, H:i'),
            'url' => route('tickets.show', encrypt($ticket->id))
        ];
        event(new NewTicketNotification($message));

        return json_encode([
            'status' => 'success',
            'sender' => \Auth::user()->first_name.' '.\Auth::user()->last_name,
            'message' => $response->content,
            'datetime' => datetime_format($response->created_at, 'j M, H:i')
        ]);
    }

    /**
     * Download Attachment
     */
    public function downloadAttachment($id)
    {
        $attachment = TicketAttachment::find(decrypt($id));
        if($attachment)
        {
            $file = $attachment->filepath;
            $path = storage_path("app/{$file}");
            if(\Storage::exists("{$file}"))
                return response()->download($path, $attachment->filename);
        }

        Alert::error(trans('controllers.file_not_exists'))->autoclose(3000);
        return redirect()->back();
    }


}
