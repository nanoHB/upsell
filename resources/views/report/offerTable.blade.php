<table class="table table-bordered table-list-offers table-hover dataTable">
    <thead>
    <tr role="row">
        <th class="ls-name">Name</th>
        <th class="ls-type">Type</th>
        <th class="ls-percent-add">Views</th>
        <th class="ls-added">Added to cart %</th>
        <th class="ls-added-amount">Added to cart</th>
        <th class="ls-added-amount">Added to cart amount</th>
    </tr>
    </thead>
    @foreach($offerList as $value)
        <tr>
            <td class="ls-name">
                <span> <a href="/offer/edit/{{$value->id}}">{{$value->name}}</a></span>
            </td>
            <td>
                {{$value->type}}
            </td>
            <td>
                {{$value->total}}
            </td>
            <td>
                @if(isset($append_data[$value->id]))
                    {{round(($append_data[$value->id]['added']/$value->total)*100,2)}} %
                @else
                    0%
                @endif
            </td>
            <td>
                @if(isset($append_data[$value->id]))
                    {{$append_data[$value->id]['added']}}
                @else
                    0
                @endif
            </td>
            <td>
                @if(isset($append_data[$value->id]))
                    {{round($append_data[$value->id]['amount'],2)}}
                @else
                    0
                @endif
            </td>
        </tr>
    @endforeach
</table>
{!! $offerList->links() !!}