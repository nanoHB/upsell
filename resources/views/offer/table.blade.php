<div class="table-responsive">
    <table class="table table-bordered table-list-offers table-hover dataTable">
        <thead>
        <tr role="row">
            <th class="ls-active">Active</th>
            <th class="ls-name">Name</th>
            <th class="ls-type">Type</th>
            <th class="ls-location">Location</th>
            <th class="ls-date">Date Created</th>
            <th class="ls-report">Report</th>
            <th class="ls-action">Delete</th>
        </tr>
        </thead>
        @foreach($listOffer as $value)
            <tr>
                <td>
                    <label class="switch-checkbox">
                        <input type="checkbox" class="change-active-status" offer-id="{{$value->id}}" @if($value->active) checked @endif data-toggle="toggle">
                        <span class="slider round"></span>
                    </label>
                </td>
                <td class="ls-name">
                    <span> <a href="{{url('/').'/offer/edit/'.$value->id}}">{{$value->name}}</a></span>
                </td>
                <td class="ls-type">
                    {{$value->type}}
                </td>
                <td class="ls-location">
                    {{$value->trigger_place}}
                </td>
                <td>
                    {{$value->created_at}}
                </td>
                <td>
                    <a href="{{url('/').'/report/offer/'.$value->id}}"><i class="fa fa-bar-chart" aria-hidden="true"></i></a>
                </td>
                <td>
                    <button class="btn btn-default button-delete-offer" offer-id="{{$value->id}}"><i class="fa fa-trash" aria-hidden="true"></i></button>
                </td>
            </tr>
        @endforeach
    </table>
</div>
{!! $listOffer->links() !!}
