<table class="table table-responsive-sm table-striped table-bordered datatable" id="reporttable">
    <thead>
        <tr>
        @foreach($header as $k => $h)
            <th>{{ $h }}</th>
        @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($records as $r)
        <tr>
            @foreach($header as $k => $h)

                @if($k == 'percentage')
                    <td align="right">{{ @$r[$k] }} %</td>
                @elseif($k == 'completion' || $k == 'status')
                    <td align="center">{{ @$r[$k] ? @lang('modules.completed') : @lang('modules.incomplete') }}</td>
                @elseif($k == 'enrolled')
                    <td align="center">{{ datetime_format(@$r[$k], 'd-M-Y' ) }}</td>
                @else
                    <td>{{ @$r[$k] }}</td>
                @endif
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>

<script>
$("#reporttable").DataTable( {
    dom: 'Bfrtip',
    buttons: [
        'csvHtml5',
        'excelHtml5',
    ],
    initComplete  : function () {
        var $buttons = $('.dt-buttons').hide();
        $('.export').on('click', function() {
            var button = $(this).data('id');
            $buttons.find('button.buttons-'+button).click();
        })
    },
} );
$('.datatable').attr('style', 'border-collapse: collapse !important');
</script>
