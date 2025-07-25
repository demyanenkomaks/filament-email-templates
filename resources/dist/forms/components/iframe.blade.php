<iframe
        src="data:text/html;base64,{{ $record->getBase64EmailPreviewData() }}"
        style="width: {{ $width??'100%' }}; min-height:1000px"
        name="{{ $name??'' }}"
>
    {{__('md-email-templates::email-templates.general-labels.browser-not-compatible')}}
</iframe>