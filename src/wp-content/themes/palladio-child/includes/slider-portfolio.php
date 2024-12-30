<?php
function category_posts_slider_shortcode($atts)
{
    wp_enqueue_style('slick-slider-css', THEME_URL . '-child' . '/assets/lib/slick/slick.css');
    wp_enqueue_style('slick-theme-css', THEME_URL . '-child' . '/assets/lib/slick/slick-theme.css');
    wp_enqueue_script('slick-slider-js', THEME_URL . '-child' . '/assets/lib/slick/slick.min.js', array('jquery'), null, true);

    // Enqueue FancyBox
    wp_enqueue_style('fancybox-css', THEME_URL . '-child' . '/assets/lib/fancybox/jquery.fancybox.min.css');
    wp_enqueue_script('fancybox-js', THEME_URL . '-child' . '/assets/lib/fancybox/jquery.fancybox.min.js', array('jquery'), null, true);

    wp_add_inline_script('slick-slider-js', "
    jQuery(document).ready(function($) {
        $('.category-slider').slick({
            slidesToShow: 4,
            slidesToScroll: 1,
            dots: true,
            infinite: true,
            responsive: [
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 1,
                        infinite: true,
                    }
                }
            ]
        });

        $('.category-menu-item').on('click', function() {
            $('.category-menu-item').removeClass('active');
            $(this).addClass('active');
            
            var categoryId = $(this).data('category-id');
            
            var targetIndex = $('.category-column[data-category-id=\"' + categoryId + '\"]').eq(0).index();
            var totalMenuItems = $('.category-menu-item').length;
            var isLastMenu = $(this).index() === totalMenuItems - 1;
            if ($(window).width() > 768) {
                targetIndex += 4;
            } else {
             if (isLastMenu) {
                    targetIndex += totalMenuItems -1 ; 
                }else {
                    targetIndex -= 1;
                }
            }
            if (targetIndex >= 0) {
                $('.category-slider').slick('slickGoTo', targetIndex);             
                $('.category-column').removeClass('slick-current');
                $('.category-column[data-category-id=\"' + categoryId + '\"]').addClass('slick-current');
            }
        });
    
        $('.category-slider').on('afterChange', function(event, slick, currentSlide){
            $('.category-column').removeClass('slick-current');
            
            var currentCategory = $('.category-column').eq(currentSlide).data('category-id');
            
            $('.category-column[data-category-id=\"' + currentCategory + '\"]').addClass('slick-current');
        });

        // Initialize FancyBox
        $('[data-fancybox]').fancybox({
            buttons: [
                'zoom',
                'close',
                'slideShow',
                'fullScreen',
            ]
        });
    });
    ");

    $categories = get_categories(array(
        'orderby'    => 'name',
        'order'      => 'ASC',
        'exclude'    => array(get_cat_ID('News')),
    ));

    if (empty($categories)) {
        return '<p>No categories found.</p>';
    }

    $desired_order = array(58, 60, 64, 61, 71, 62, 63, 70); 
    usort($categories, function ($a, $b) use ($desired_order) {
        $pos_a = array_search($a->term_id, $desired_order);
        $pos_b = array_search($b->term_id, $desired_order);

        $pos_a = $pos_a === false ? PHP_INT_MAX : $pos_a;
        $pos_b = $pos_b === false ? PHP_INT_MAX : $pos_b;

        return $pos_a - $pos_b;
    });

    ob_start();
?>
    <div class="category-menu">
        <ul class="category-menu-list">
            <?php foreach ($categories as $index => $category): ?>
                <?php $category_icon = get_term_meta($category->term_id, 'icon', true); ?>
                <li class="category-menu-item <?php echo $index === 0 ? 'active' : ''; ?>" data-category-id="<?php echo esc_attr($category->term_id); ?>">
                    <div class="category-menu-item-inner">
                        <?php if ($category_icon): ?>
                            <img width="40" height="40" src="<?php echo esc_url($category_icon); ?>" alt="<?php echo esc_attr($category->name); ?>" class="category-icon">
                        <?php endif; ?>
                        <p><?php echo esc_html($category->name); ?></p>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="category-slider">
        <?php foreach ($categories as $index => $category): ?>
            <div class="category-column <?php echo $index === 0 ? 'active' : ''; ?>" id="category-<?php echo esc_attr($category->term_id); ?>" data-category-id="<?php echo esc_attr($category->term_id); ?>">
                <div class="category-title">
                    <p><?php echo esc_html($category->name); ?></p>
                </div>
                <ul class="posts-list">
                    <?php
                    $posts = get_posts(array(
                        'category' => $category->term_id,
                        'numberposts' => 3,
                        'order'      => 'ASC',
                    ));

                    if (!empty($posts)) {
                        foreach ($posts as $post) {
                            $thumb_url = get_the_post_thumbnail_url($post->ID, 'medium');
                            $thumb_url = $thumb_url ? $thumb_url : 'https://via.placeholder.com/300x180';
                    ?>
                            <li class="post-item">
                                <div class="post-thumb-wrap">
                                    <a class="post-thumb-link" data-fancybox="gallery" href="<?php echo esc_url($thumb_url); ?> ">
                                        <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr(get_the_title($post->ID)); ?>" class="post-thumb">
                                    </a>
                                </div>
                            </li>
                    <?php
                        }
                    } else {
                        echo '<li>No posts found</li>';
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

// Register the shortcode
function custom_boxes_shortcode() {
    return '
    <div class="custom-box-container">
        <a href="https://www.namaik.com/our-valuable-team/" target="_blank">
            <div class="custom-box img">
                <img src="/wp-content/uploads/2024/12/Screenshot-from-2024-12-30-11-02-40.png" alt="Our Partners">
                <span>Our Partners</span>
            </div>
        </a>
        <a href="https://www.3pecotecture.com/contact_staff.html" target="_blank">
            <div class="custom-box img">
                <img src="/wp-content/uploads/2024/12/images.jpeg" alt="Affiliated Firm">
                <span>Affiliated Firm</span>
            </div>
        </a>
        <a href="https://scholarbank.nus.edu.sg/handle/10635/242199" target="_blank">
            <div class="custom-box">
                <span>Thesis</span>
            </div>
        </a>
    </div>';
}
add_shortcode('custom_boxes', 'custom_boxes_shortcode');
?>