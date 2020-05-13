<script type="text/javascript">
    window.translationModeActive = {{ $enabled }};

    @if($enabled)
        window.inlineTranslations = (function() {
            var matches = document.body.innerHTML.matchAll(/\~\~\#([a-zA-Z\.\-\_]*)\#\~\#(.*?)\#\~\~/gs);
            for (var match of matches) {
                document.body.innerHTML = document.body.innerHTML.replace(
                    match[0],
                    '<span data-translation-key="' + match[1] + '">' + match[2] + '</span>'
                );
            }
        })();
    @endif
</script>
