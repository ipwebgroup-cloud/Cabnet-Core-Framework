<?php
declare(strict_types=1);

/**
 * Legacy compatibility wrapper.
 *
 * Rendering ownership now lives in src/View/PhpRenderer. This wrapper keeps
 * existing global-class usages working during the hybrid transition.
 */
final class PhpRenderer extends \Cabnet\View\PhpRenderer implements RendererInterface
{
}
