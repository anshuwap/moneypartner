<style>
    .page-link {
        color: #2fc296;
    }
</style>
@if ($paginator->hasPages())
<div class="row">
<div class="col-md-6 is-5 mt-4">
    <?php
    $perPage = (!empty($perPage)) ? (int)$perPage : config('constants.perPage');
    $first_record = $paginator->firstItem();
    $current_record = ($perPage * ($paginator->currentPage() - 1)) + $paginator->count();
    $total_record = $paginator->total();

    echo "Showing $first_record to  $current_record of $total_record Results.";

    ?>
</div>
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