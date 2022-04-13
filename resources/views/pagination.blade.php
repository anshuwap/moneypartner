<style>
    .page-link {
        color: #2fc296;
    }
</style>

<div class="row">
    <?php

use Illuminate\Http\Request;

    $perPage = (!empty($_GET['perPage'])) ? (int)$_GET['perPage'] : config('constants.perPage'); ?>
    <div class="col-md-1 is-5 mt-3">
        <select class="form-control-sm form-control" name="perPare" id="perPage">
            <!-- <option value="10" {{ ($perPage =="10" )?"selected":"" }}>10</option> -->
            <option value="20" {{ ($perPage =="20" )?'selected':''}}>20</option>
            <option value="50" {{ ($perPage =="50" )?'selected':''}}>50</option>
            <option value="100" {{ ($perPage =="100" )?'selected':''}}>100</option>
            <option value="200" {{ ($perPage =="200" )?'selected':''}}>200</option>
            <option value="500" {{ ($perPage =="500" )?'selected':''}}>500</option>
        </select>
    </div>
    <div class="col-md-5 is-5 mt-4">
        <?php
        $perPage = (!empty($_GET['perPage'])) ? (int)$_GET['perPage'] : config('constants.perPage');
        $first_record = $paginator->firstItem();
        $current_record = ($perPage * ($paginator->currentPage() - 1)) + $paginator->count();
        $total_record = $paginator->total();

        echo "Showing $first_record to  $current_record of $total_record Results.";

        ?>
    </div>
    @if ($paginator->hasPages())
    <div class="col-md-6">
        <ul class="pagination justify-content-end">

            @if ($paginator->onFirstPage())
            <li class="disabled page-item"><span class="page-link">&laquo;</span></li>
            @else
            <li><a href="{{ $paginator->previousPageUrl() }}" class="page-link" rel="prev">&laquo;</a></li>
            @endif


            @foreach ($elements as $element)

            @if (is_string($element))
            <li class="disabled page-item"><span>{{ $element }}</span></li>
            @endif


            @if (is_array($element))
            @foreach ($element as $page => $url)
            @if ($page == $paginator->currentPage())
            <li class="active my-active page-item"><span class="page-link">{{ $page }}</span></li>
            @else
            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
            @endif
            @endforeach
            @endif
            @endforeach


            @if ($paginator->hasMorePages())
            <li class="page-item"><a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">&raquo;</a></li>
            @else
            <li class="disabled page-item"><span class="page-link">&raquo;</span></li>
            @endif
        </ul>
    </div>

    @endif
</div>
@push('custom-script')
<?php
$x = $_SERVER['REQUEST_URI'];
$parsed = parse_url($x);
$string = '';
if(!empty($parsed['query'])){
$query = $parsed['query'];
parse_str($query, $params);
unset($params['perPage']);
$string = http_build_query($params);
}
?>
<script>
    $('#perPage').change(function() {
        var query = '<?= $string ?>';
        var perPage = $(this).val();
        location.href = "{{ url()->current()}}?perPage=" + perPage + '&' + query;
    })
</script>

@endpush