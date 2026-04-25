<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Standard page title + breadcrumb (matches app-wide “modern” screen header).
 *
 * Pass from a view:
 *   $this->load->view('partials/page_header', array(
 *     'title'         => string (page heading, can be lang() output),
 *     'icon'          => optional Font Awesome classes, default fas fa-file-alt text-primary mr-2
 *     'breadcrumbs'  => list of: array('label' => string, 'url' => string|null)
 *                        Last item should use 'url' => null (or omit) for the active crumb.
 *   ));
 */
$ph_title = isset($title) ? $title : '';
$ph_icon = isset($icon) && $icon !== '' ? $icon : 'fas fa-file-alt text-primary mr-2';
$ph_crumbs = (isset($breadcrumbs) && is_array($breadcrumbs)) ? $breadcrumbs : array();
$ph_id = !empty($header_id) ? (string) $header_id : 'ap-page-header';
?>
<section class="content-header py-3 border-bottom bg-white"<?php echo $ph_id !== '' ? ' id="' . htmlspecialchars($ph_id, ENT_QUOTES, 'UTF-8') . '"' : ''; ?>>
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-12 col-lg-8">
                <h1 class="h3 mb-1 font-weight-bold text-dark">
                    <i class="<?php echo htmlspecialchars($ph_icon, ENT_QUOTES, 'UTF-8'); ?>"></i>
                    <?php echo $ph_title; ?>
                </h1>
                <?php if (!empty($ph_crumbs)) : ?>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 py-0 small">
                        <?php
                        $n = count($ph_crumbs);
                        foreach ($ph_crumbs as $i => $crumb) {
                            if (!is_array($crumb)) {
                                continue;
                            }
                            $label = isset($crumb['label']) ? $crumb['label'] : '';
                            $url = array_key_exists('url', $crumb) ? $crumb['url'] : null;
                            $is_last = ($i === $n - 1);
                            if ($is_last) {
                                echo '<li class="breadcrumb-item active" aria-current="page">' . $label . '</li>';
                            } else {
                                $u = htmlspecialchars((string) $url, ENT_QUOTES, 'UTF-8');
                                echo '<li class="breadcrumb-item"><a href="' . $u . '">' . $label . '</a></li>';
                            }
                        }
                        ?>
                    </ol>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
