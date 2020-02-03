@extends('layouts.app')
@section('title', 'Laravel Shop')

@section('content')
<div class="row">
  <div class="col-lg-3 ">
        
  </div>






  <div class="col-lg-9 shadow-lg p-3 mb-5 bg-white rounded">
    <div class="card">
      <div class="card-body">
        <!-- 筛选组件开始 -->
        <!-- 筛选组件结束 -->
        <form action="{{ route('products.index') }}" class="search-form">
          <div class="form-row">
              <div class="form-row mb-4 col-8">
                <div class="col-auto"><input required type="text" class="form-control form-control" name="search" placeholder="Tìm kiếm ...."></div>
                <div class="col-auto"><button class="btn btn-primary"><i class="fas fa-search"></i></button></div>
              </div>
        
              <div class="col-4 justify-content">
                <select name="order" class="form-control form-control float-right">
                  <option value="">Lọc theo</option>
                  <option value="price_asc">Giá từ thấp đến cao</option>
                  <option value="price_desc">Giá từ cao xuống thấp</option>
                  <option value="sold_count_desc">Giảm giá từ cao xuống thấp</option>
                  <option value="sold_count_asc">Giảm giá từ thấp đến cao</option>
                  <option value="rating_desc">A-Z</option>
                  <option value="rating_asc">Z-A</option>
                </select>
              </div>    
          </div>
        </form>



        <div class="row products-list">
          @foreach($products as $product)
            <div  class="col-lg-3 col-md-4 col-sm-6 product-item text-center">
              <div class="product-content">
                <div class="top">
                  <div class="img">
                    <a href="{{ route('products.show', ['product' => $product->id]) }}">
                      <img class="rounded" style="width:100% ,height:10%"  src="{{ $product->image_url }}" alt="123">
                    </a>
                  </div>
                  <div class="price"><b>$</b>{{ $product->price }}</div>
                  <div class="title">
                    <a href="{{ route('products.show', ['product' => $product->id]) }}">{{ $product->title }}</a>
                  </div>
                  <a href="{{ route('products.show', ['product' => $product->id]) }}"><button class="btn btn-primary mb-1"><i class="fa fa-spinner fa-pulse fa-fw "></i> Xem Chi Tiết Sản Phẩm</button></a>
                </div>
                <div class="bottom">
                  <div class="sold_count">Giảm Giá <span>{{ $product->sold_count }}</span></div>
                  <div class="review_count">Đánh Giá <span>{{ $product->review_count }}</span></div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
        <div class="float-right">{{ $products->appends($filters)->render() }}</div>  <!-- 只需要添加这一行 -->
      </div>
    </div>
  </div>

</div>
@endsection

@section('scriptsAfterJs')
<script>
  var filters = {!! json_encode($filters) !!};
  $(document).ready(function () {
    $('.search-form input[name=search]').val(filters.search);
    $('.search-form select[name=order]').val(filters.order);
    $('.search-form select[name=order]').on('change', function() {
      $('.search-form').submit();
     
    });
  })
</script>
@endsection
