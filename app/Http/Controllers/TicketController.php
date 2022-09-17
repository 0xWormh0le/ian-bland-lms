<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ticket;
use App\TicketResponse;
use App\TicketAttachment;
use App\TicketHistory;
use App\User;
use Yajra\Datatables\Datatables;
use Alert, Auth, Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Events\NewTicketNotification;
use App\Events\NewTicketResponseNotification;

class TicketController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

      //  if(Auth::user()->isSysAdmin() || Auth::user()->isClientAdmin())
       if(validate_role('tickets.index'))
        {
            // Show Dashboard
            $title = session('menuLabel')['tickets.dashboard'];

            $active = Ticket::where('status', '!=', 'closed');
            $closed = Ticket::where('status', '=', 'closed');
            $unread = Ticket::where('read_by_admin', '=', false);

            if(Auth::user()->isSysAdmin())
            {
              $user_id = Auth::user()->id ;

              $active->where('assigned_to',$user_id);
              $closed->where('assigned_to',$user_id);
              $unread->where('assigned_to',$user_id);

            }
            else if(Auth::user()->isClientAdmin())
            {
              $company_id = Auth::user()->company_id;
              $user_id = Auth::user()->id ;

              $active->where('created_by', '!=', $user_id)->where('company_id', $company_id);
              $closed->where('created_by', '!=', $user_id)->where('company_id', $company_id);
              $unread->where('created_by', '!=', $user_id)->where('company_id', $company_id);
            }
            else {
              $company_id = Auth::user()->company_id;
              $user_id = Auth::user()->id ;

              $active->where('created_by', '!=', $user_id)->where('assigned_to',$user_id)->where('company_id', $company_id);
              $closed->where('created_by', '!=', $user_id)->where('assigned_to',$user_id)->where('company_id', $company_id);
              $unread->where('created_by', '!=', $user_id)->where('assigned_to',$user_id)->where('company_id', $company_id);
            }

            $active_count = $active->count();
            $closed_count = $closed->count();
            $unread_count = $unread->count();


            return view('tickets.dashboard', compact('title', 'active_count', 'closed_count', 'unread_count'));
        }
       else{
         Alert::error(__('messages.unauthorized'));
         return redirect('/');
        }
    }

    public function create()
    {
        $breadcrumbs = [
            route('my-tickets.index') => trans('modules.my_tickets'),
            '' => trans('modules.create_ticket'),
        ];
        return view('tickets.create', compact('breadcrumbs'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function open()
    {

        //if(Auth::user()->isSysAdmin() || Auth::user()->isClientAdmin())
        if(validate_role('tickets.open'))
        {
            $title = session('menuLabel')['tickets.open'];

            $roles = \App\Role::getByRole('tickets.respond');
            $ticketHandler = User::whereIn('role_id', $roles)
                            ->where('id', '<>', Auth::id())
                            ->orWhere(function ($query) {
                                $query->whereNull('company_id')
                                    ->whereNull('role_id')
                                    ->where('id', '<>', Auth::id());
                            })
                            ->get();

            return view('tickets.open', compact('title', 'ticketHandler'));
        }
        Alert::error(__('messages.unauthorized'));
        return redirect('/');
    }

    public function closed()
    {
        //if(Auth::user()->isSysAdmin() || Auth::user()->isClientAdmin())
        if(validate_role('tickets.closed'))
        {
            $title = session('menuLabel')['tickets.closed'];
            return view('tickets.closed', compact('title'));
        }
        Alert::error(__('messages.unauthorized'));
        return redirect('/');
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData($status)
    {
        if($status !== 'closed')
             $status = 'open';


        $canAssign = validate_role('tickets.assign');

        $data = Ticket::select(
            'tickets.id',
            'tickets.created_at',
            'tickets.content',
            'tickets.sender_name',
            'tickets.ticket_number',
            'tickets.status',
            'tickets.read_by_admin',
            'tickets.read_by_client_admin',
            'tickets.read_by_user',
            'tickets.assigned_to',
            'users.first_name',
            'users.last_name',
            'companies.company_name'
        )
            ->leftJoin('users', 'users.id', 'assigned_to')
            ->leftJoin('companies', 'companies.id', 'tickets.company_id')
            ->where('status', $status);

        if(Auth::user()->isSysAdmin())
        {
          $user_id = Auth::user()->id ;
          $data->where('assigned_to',$user_id);

        }
        else if(Auth::user()->isClientAdmin())
        {
          $company_id = Auth::user()->company_id;
          $user_id = Auth::user()->id ;

          $data->where('tickets.created_by', '!=', $user_id)
                ->where('tickets.company_id', $company_id);
        }
        else
        {
          $user_id = Auth::user()->id ;
          $company_id = Auth::user()->company_id;
          $data->where('tickets.created_by', '!=', $user_id)
                ->where('tickets.assigned_to',$user_id)
                ->where('tickets.company_id', $company_id);

        }

      //  if(!$canAssign)
        //    $data->where('assigned_to', Auth::id());

        return Datatables::of($data)
                        ->editColumn('content', function ($data) {
                            return get_words($data->content);
                        })
                        ->editColumn('status', function ($data) {
                            $read = 0;

                            if (Auth::user()->isClientAdmin()) {
                                $read = $data->read_by_client_admin;
                            } else if (Auth::user()->isSysAdmin()) {
                                $read = $data->read_by_admin;
                            } else {
                                $read = $data->read_by_user;
                            }

                            return $data->status.' '.(!$read ? '<span class="badge badge-pill badge-danger">'.trans('controllers.unread').'</span>' : '');
                        })
                        ->editColumn('first_name', function ($data) {
                            if($data->assigned_to)
                                return $data->first_name.' '.$data->last_name;
                            return '-';
                        })
                        ->addColumn('action', function ($data) use ($canAssign) {
                            $html = '<a href="'.route('tickets.show', encrypt($data->id)).'" class="btn btn-sm btn-info">'.trans('controllers.view').'</a>';

                            if($canAssign && $data->status !="closed")
                                $html .= ' <button type="button" class="btn btn-sm btn-warning assign-ticket" data-id="'.$data->id.'">'.trans('controllers.assign_to').'</button>';

                            return $html;
                        })
                        ->rawColumns(['action', 'status'])
                        ->make(true);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'chat_name' => 'required',
            'chat_email' => 'required',
            'chat_message' => 'required',
        ];
        $request->validate($rules);
        $attachment_id = [];

        if ($request->has('attachment')) {
            foreach($request->file('attachment') as $file) {
                $filename = $file->getClientOriginalName();
                $filesize = $file->getClientSize();
                $filepath = $file->store('ticket_attachments');

                $attachment = new TicketAttachment;
                $attachment->filename = $filename;
                $attachment->filesize = $filesize;
                $attachment->filepath = $filepath;
                $attachment->save();
                $attachment_id[] = $attachment->id;
            }
        }

        $record = new Ticket;
        $record->company_id = Auth::user()->company_id;
        $record->source = 'LMS';
        $record->ticket_number = strtotime(date('Y-m-d H:i:s'));
        $record->sender_name = $request->chat_name;
        $record->sender_email = $request->chat_email;
        $record->content = $request->chat_message;
        $record->status = 'open';
        $record->attachment_id = implode(',', $attachment_id);
        $record->created_by = \Auth::id();
        if (auth()->user()->isClientAdmin()) {
            $record->assigned_to = 1;
        }
        $record->save();

        $history = new TicketHistory;
        $history->ticket_id = $record->id;
        $history->action = 'Ticket Opened';
        $history->ticket_status = $record->status;
        $history->user_id = $record->created_by;
        $history->save();

        if ($record->save()) {
            $message = [
                'id' => $record->ticket_number,
                'sender' => $record->sender_name,
                'assigned_to' => @$record->assigned_to ?: 0,
                'message' => get_words($record->content, 10),
                'datetime' => datetime_format($record->created_at, 'j M, H:i'),
                'url' => route('tickets.show', encrypt($record->id))
            ];
            event(new NewTicketNotification($message));

            $data = [
                'ticket' => $record,
                'user_id' => auth()->user()->id
            ];
            dispatch(new \App\Jobs\SendEmail($data, 'NewTicketEmail'));
        }

        return redirect()->route('my-tickets.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Ticket::select('tickets.*', 'companies.company_name')->leftJoin('companies', 'companies.id', 'tickets.company_id')
                 ->where('tickets.id', decrypt($id))->first();
        if($data)
        {
            if(!$data->opened_at && Auth::user()->isSysAdmin()){
                $data->opened_at = date('Y-m-d H:i:s');
                $data->read_by_admin = true;
                $data->save();

                $history = new TicketHistory;
                $history->ticket_id = $data->id;
                $history->ticket_status = $data->status;
                $history->action = 'Ticket Viewed';
                $history->user_id = Auth::id();
                $history->save();
            }
            elseif(!$data->read_by_admin && Auth::user()->isSysAdmin())
            {
                $data->read_by_admin = true;
                $data->save();
            }

            if(!$data->opened_at && Auth::user()->isClientAdmin()){
                $data->opened_at = date('Y-m-d H:i:s');
                $data->read_by_client_admin = true;
                $data->save();

                $history = new TicketHistory;
                $history->ticket_id = $data->id;
                $history->ticket_status = $data->status;
                $history->action = 'Ticket Viewed';
                $history->user_id = Auth::id();
                $history->save();
            }
            elseif(!$data->read_by_admin && Auth::user()->isClientAdmin())
            {
                $data->read_by_client_admin = true;
                $data->save();
            }

            $breadcrumbs = [];
            if($data->status !== 'closed')
                $breadcrumbs[route('tickets.open')] = trans('controllers.open_tickets');
            else
                $breadcrumbs[route('tickets.closed')] = trans('controllers.closed_tickets');

            $breadcrumbs[''] = $data->ticket_number;
            $title = trans('controllers.details_of_ticket').' #'.$data->ticket_number;
            $histories = TicketHistory::where('ticket_id', $data->id)->orderBy('created_at', 'DESC')->get();
            return view('tickets.details', compact('title', 'breadcrumbs', 'data', 'histories'));
        }
        Alert::error(__('messages.invalid_request'));
        return redirect()->route('tickets.open');
    }


    public function response(Request $request)
    {
        $response = new TicketResponse;
        $response->ticket_id = $request->ticket_id;
        $response->responder_id = Auth::id();
        $response->content = $request->content;
        $response->save();

        $ticket = Ticket::find($request->ticket_id);

        if(Auth::user()->isClientAdmin())
        {
           $ticket->read_by_admin = true;
        }
        else
        $ticket->read_by_client_admin = true;

        $ticket->read_by_user = false;
        $ticket->save();

        $message = [
            'id' => $response->ticket->ticket_number,
            'user_id' => $response->ticket->created_by,
            'sender' => \Auth::user()->first_name.' '.\Auth::user()->last_name,
            'message' => get_words($response->content),
            'datetime' => datetime_format($response->created_at, 'j M, H:i'),
            'url' => route('my-tickets.show', encrypt($response->ticket_id))
        ];
        event(new NewTicketResponseNotification($message));
        dispatch(new \App\Jobs\SendEmail($response, 'ResponseTicketEmail'));

        return json_encode([
            'status' => 'success',
            'sender' => $response->user->first_name.' '.$response->user->last_name,
            'message' => $response->content,
            'datetime' => datetime_format($response->created_at, 'j M, H:i'),
        ]);
    }

    public function setStatus(Request $request, $id)
    {
        $ticket = Ticket::find(decrypt($id));
        if($ticket)
        {
            if($ticket->status == 'closed')
            {
                $newStatus = trans('controllers.ticket_re_opened');
                $ticket->status = 'open';
            }else{
                $newStatus = trans('controllers.ticket_closed');
                $ticket->status = 'closed';
            }
            if($ticket->save())
            {
                $history = new TicketHistory;
                $history->ticket_id = $ticket->id;
                $history->action = $newStatus;
                $history->ticket_status = $ticket->status;
                $history->user_id = \Auth::id();
                $history->save();
            }

            Alert::success($newStatus);
            return redirect()->route('tickets.show', $id);
        }
        Alert::error(__('messages.invalid_request'));
        return redirect()->back();
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

        Alert::error('File not exists')->autoclose(3000);
        return redirect()->back();
    }

    /**
     * Handle ticket assignment
     */
    public function assignTo(Request $request)
    {
        $ticket = Ticket::find($request->ticket_id);
        if($ticket)
        {
            $user = User::find($request->assigned_to);

            $ticket->assigned_to = $request->assigned_to;
            $ticket->assigned_at = date('Y-m-d H:i:s');
            $ticket->assigned_by = Auth::id();
            if($ticket->save())
            {
                $history = new TicketHistory;
                $history->ticket_id = $ticket->id;
                $history->action = 'Ticket Assigned';
                $history->comments = trans('controllers.ticket_assigned_to').' '.$user->first_name.' '.$user->last_name;
                $history->ticket_status = $ticket->status;
                $history->user_id = Auth::id();
                $history->save();

                $type = 'success';
                $msg = trans('messages.success');
                $detail = $history->comments;
            }

        }else{
            $type = 'error';
            $msg = trans('messages.error');
            $detail = trans('controllers.ticket_is_invalid');
        }

        return json_encode([
            'type' => $type,
            'msg' => $msg,
            'detail' => $detail,
        ]);
    }

    public function getAssignee(Request $request)
    {

         $ticketId = $request->ticket_id;

         $company = Ticket::select('company_id')->where('id', $ticketId)->first();

        $users = array();
        if($company && $company->company_id > 0)
        {
          $users = User::select('users.id','first_name','last_name', 'role_id')
                         ->join('roles', 'roles.id', 'users.role_id')
                         ->where('users.company_id', $company->company_id)
                         ->where('users.id', '!=', \Auth::id())
                         ->where('roles.role_access', 'like', '%tickets.assign%')
                         ->get();

          $suusers = User::select('users.id','first_name','last_name')
                        ->where('role', 0)
                        ->where('role_id', 0)
                        ->where('users.id', '!=', \Auth::id())
                        ->get();


            $users = $suusers->merge($users);
        }

        return response()->json($users);

    }

}
