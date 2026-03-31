<?php
declare(strict_types=1);

/**
 * Legacy compatibility contract.
 *
 * The canonical renderer contract now lives in src/View/Renderer.php.
 * Keep this interface so existing app-layer type hints remain valid while
 * framework ownership migrates toward src/.
 */
interface RendererInterface extends \Cabnet\View\Renderer
{
}
