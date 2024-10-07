<div class="mw-350px">{{optional($data->item)->{'name_' . lang()} ?? '--'}}</div>
<span class="badge badge-primary">{{optional($data->barcodes)->pluck('barcode')->implode(', ') ?? __('No Barcode')}}</span>
