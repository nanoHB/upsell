<div class="widget">
    <div class="infor">
        <span>{{$title}}</span>
        <span>{{$description}}</span>
    </div>
    <ul>
    @foreach($data as $value)
    @if(sizeof($value->variants)>1)
    @foreach($value->variants as $item)
    @if(in_array($item->id,$listVariant))
    <li>
            <span>
                <a href="{{'/products/'.$value->handle}}">
                    <img src="{{$value->image->src}}" alt="{{$value->title}}">
                </a>
                <span>{{$value->title}}</span>
                <span>{{$item->title}}</span>
                <span>{{$item->price}}</span>
            </span>
        <button type="button" class="add-to-cart" offer-type="{{$type}}" variant-id="{{$item->id}}"  product-id="{{$value->id}}">Add to cart</button>
    </li>
    @endif
    @endforeach
    @else
    <li>
            <span>
                <a href="{{'/products/'.$value->handle}}">
                    <img src="{{$value->image->src}}" alt="{{$value->title}}">
                </a>
                <span>{{$value->title}}</span>
                <span>{{$value->variants[0]->price}}</span>
            </span>
        <button type="button" class="add-to-cart" offer-type="{{$type}}" variant-id="{{$value->variants[0]->id}}" product-id="{{$value->id}}">Add to cart</button>
    </li>
    @endif
    @endforeach
    </ul>
</div>