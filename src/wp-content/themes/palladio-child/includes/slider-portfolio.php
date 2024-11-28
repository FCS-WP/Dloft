<?php

function enqueue_slick_slider_assets()
{

    global $post;
    if (isset($post->post_content) && has_shortcode($post->post_content, 'category_posts_slider')) {
        wp_enqueue_style('slick-slider-css', 'https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick.css');
        wp_enqueue_style('slick-theme-css', 'https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick-theme.css');

        wp_enqueue_script('slick-slider-js', 'https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick.min.js', array('jquery'), null, true);

        wp_add_inline_script('slick-slider-js', "
            jQuery(document).ready(function($) {
                $('.category-slider').slick({
                    slidesToShow: 4,
                    slidesToScroll: 1,
                    infinite: false,
                    dots: true,
                    responsive: [
                        {
                            breakpoint: 768,
                            settings: {
                                slidesToShow: 1
                            }
                        }
                    ]
                });
            });
        ");
    }
}
add_action('wp_enqueue_scripts', 'enqueue_slick_slider_assets');


function category_posts_slider_shortcode($atts)
{
    $categories = get_categories(array(
        'orderby'    => 'name',
        'order'      => 'ASC',
        'exclude'    => array(get_cat_ID('News')),
    ));

    if (empty($categories)) {
        return '<p>Không tìm thấy danh mục nào.</p>';
    }

    ob_start();
?>
    <div class="category-menu">
        <ul class="category-menu-list">
            <?php foreach ($categories as $category): ?>
                <?php $category_icon = get_term_meta($category->term_id, 'icon', true);
                ?>
                <li class="category-menu-item">
                    <!-- <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="category-menu-link"> -->
                    <div>
                        <?php if ($category_icon): ?>
                            <img width="40" height="40" src="<?php echo esc_url($category_icon); ?>" alt="<?php echo esc_attr($category->name); ?>" class="category-icon">
                        <?php endif; ?>
                        <p><?php echo esc_html($category->name); ?>
                        <p>
                    </div>
                    <!-- </a> -->
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="category-slider">
        <?php foreach ($categories as $category): ?>
            <div class="category-column" id="category-<?php echo esc_attr($category->term_id); ?>">
                <?php $category_icon = get_term_meta($category->term_id, 'icon', true);
                ?>
                <ul class="posts-list">
                    <?php
                    $posts = get_posts(array(
                        'category' => $category->term_id,
                        'numberposts' => 3,
                    ));

                    if (!empty($posts)) {
                        foreach ($posts as $post) {
                            $thumb_url = get_the_post_thumbnail_url($post->ID, 'medium');
                            $thumb_url = $thumb_url ? $thumb_url : 'https://via.placeholder.com/300x180';
                    ?>
                            <li class="post-item">
                                <div class="post-thumb-wrap">
                                    <a  class="post-thumb-link">
                                        <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr(get_the_title($post->ID)); ?>" class="post-thumb">
                                        <span class="post-title"><?php echo esc_html(get_the_title($post->ID)); ?></span>
                                    </a>
                                </div>
                            </li>
                    <?php
                        }
                    } else {
                        echo '<li>Không có bài viết.</li>';
                    }
                    ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>
<?php
    return ob_get_clean();
}

add_shortcode('category_posts_slider', 'category_posts_slider_shortcode');

?>