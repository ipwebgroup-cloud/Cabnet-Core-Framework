<?php
declare(strict_types=1);

/**
 * Legacy compatibility wrapper.
 *
 * Rendering ownership now lives in src/View/TwigRenderer. This wrapper keeps
 * existing global-class usages working during the hybrid transition.
 */
final class TwigRenderer extends \Cabnet\View\TwigRenderer implements RendererInterface
{
}
