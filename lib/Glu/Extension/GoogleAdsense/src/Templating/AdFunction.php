<?php

namespace Glu\Extension\GoogleAdsense\Templating;

use Glu\Templating\_Function;

final class AdFunction implements _Function
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function name(): string
    {
        return 'adsense_ad';
    }

    public function callable(): callable
    {
        return function (string $slot, string $format = 'link') {
            return <<<CODE
<ins class="adsbygoogle"
	 style="display:block"
	 data-ad-client="$this->id"
	 data-ad-slot="$slot"
	 data-ad-format="$format"
	 data-full-width-responsive="true"></ins>
<script>
	(adsbygoogle = window.adsbygoogle || []).push({});
</script>
CODE;
        };
    }

    public function escape(): bool
    {
        return false;
    }
}
