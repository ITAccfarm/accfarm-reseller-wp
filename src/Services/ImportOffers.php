<?php

namespace Src\Services;

use Closure;
use Exception;
use ITAccfarm\ResellerSDK\ResellerSDK;
use Src\Traits\Singleton;

class ImportOffers
{
    use Singleton;

    /**
     * @var ResellerSDK
     */
    private $accfarmApi;

    /**
     * @var array
     */
    private $productImages = [];

    private $ajaxEndpoints = [
        'categories' => 'accfarm_get_categories_data',
        'offers' => 'accfarm_get_offers_data',

        'importCategories' => 'accfarm_get_categories_import_ids',
        'importProducts' => 'accfarm_get_products_import_ids',
        'importOffers' => 'accfarm_get_offers_import_ids',

        'importAll' => 'accfarm_get_offers_import_all',
    ];

    public function __construct()
    {
        $this->accfarmApi = Accfarm::instance()->api();
    }

    public function register() {
        foreach ($this->ajaxEndpoints as $name => $endpoint) {
            add_action("wp_ajax_nopriv_$endpoint", [$this, "{$name}Ajax"]);
            add_action("wp_ajax_$endpoint", [$this, "{$name}Ajax"]);
        }
    }

    public function getCategories(): array
    {
        if (!Accfarm::instance()->authenticate()) {
            return [];
        }

        return $this->accfarmApi->categories();
    }

    public function getOffers(array $data): array
    {
        if (!Accfarm::instance()->authenticate()) {
            return [];
        }

        return $this->accfarmApi->offers($data);
    }

    public function categoriesAjax()
    {
        $this->callback(function () {
            $categories = $this->getCategories();

            if (empty($categories)) {
                throw new Exception('Categories are empty!');
            }

            wp_send_json_success($categories);
            return;
        });
    }

    public function offersAjax()
    {
        $this->callback(function () {
            $data = $this->getPostData([
                'product_id' => 'required'
            ]);

            $offers = $this->getOffers($data);

            if (empty($offers)) {
                throw new Exception('Offers are empty!');
            }

            wp_send_json_success($offers);
            return;
        });
    }

    public function importCategoriesAjax()
    {
        $this->callback(function () {
            $data = $this->getPostData([
                'categories' => 'required',
            ]);

            $offers = [];

            foreach ($data['categories'] as $category) {
                if (!empty($category)) {
                    foreach ($this->getOffers(['category_id' => $category]) as $offer) {
                        array_push($offers, $offer);
                    }
                }
            }

            if (empty($offers)) {
                throw new Exception('Offers are empty!');
            }

            $this->createProducts($offers);

            wp_send_json_success(true);
            return;
        });
    }

    public function importProductsAjax()
    {
        $this->callback(function () {
            $data = $this->getPostData([
                'products' => 'required',
            ]);

            $offers = [];

            foreach ($data['products'] as $product) {
                if (!empty($product)) {
                    foreach ($this->getOffers(['product_id' => $product]) as $offer) {
                        array_push($offers, $offer);
                    }
                }
            }

            if (empty($offers)) {
                throw new Exception('Offers are empty!');
            }

            $this->createProducts($offers);

            wp_send_json_success(true);
            return;
        });
    }

    public function importOffersAjax()
    {
        $this->callback(function () {
            $data = $this->getPostData([
                'offers' => 'required',
            ]);

            if (empty($data['offers'])) {
                throw new Exception('Offers are empty!');
            }

            $this->createProducts($data['offers']);

            wp_send_json_success(true);
            return;
        });
    }

    public function importAllAjax()
    {
        $this->callback(function () {
            $offers = $this->getOffers([]);

            $this->createProducts($offers);

            wp_send_json_success(true);
            return;
        });
    }

    private function createProducts(array $offers)
    {
        $categories = $this->getCategories();

        if (empty($categories)) {
            return;
        }

        $options = $this->getPostData([
            'publish' => '',
            'setPrices' => '',
            'margin' => '',
            'marginType' => ''
        ]);

        $postStatus = $options['publish'] ? 'publish' : 'draft';

        foreach ($offers as $offer) {
            $description = !empty($offer['preview_text']) ? $offer['preview_text'] : '';

            if (!empty($offer['instruction'])) {
                $description .=  "<h4>Instructions:</h4> {$offer['instruction']}";
            }

            $post_args = [
                'post_author' => get_current_user(),
                'post_title' => $offer['name'],
                'post_content' => $description,
                'post_type' => 'product',
                'post_status' => $postStatus
            ];

            $post_id = wp_insert_post($post_args);

            $type = 'offer';

            if ((int) $offer['type'] == 0) {
                $type = 'offer';
            } elseif ((int) $offer['type'] == 1) {
                $type = 'review';
            } elseif ((int) $offer['type'] == 2) {
                $type = 'install';
            }

            update_post_meta($post_id, '_accfarm_product_type', $type);
            update_post_meta($post_id, '_accfarm_offer_id', $offer['id']);

            if ($options['setPrices']) {
                $this->setProductPrices($offer, $post_id);
            }

            $this->setProductImage($categories, $offer, $post_id);
        }
    }

    private function setProductImage($categories, $offer, $post_id)
    {
        $imageData = $this->getPreviewImage($categories, $offer);

        if (empty($imageData)) {
            return;
        }

        if (!empty($imageData['imageId'])) {
            set_post_thumbnail($post_id, $imageData['imageId']);
            return;
        }

        if (empty($imageData['imageUrl'])) {
            return;
        }

        $imgArray = ['name' => wp_basename($imageData['imageUrl']), 'tmp_name' => download_url($imageData['imageUrl'])];

        if (!is_wp_error($imgArray['tmp_name'])) {
            $imageId = media_handle_sideload($imgArray, $post_id);

            if (is_wp_error($imageId)) {
                @unlink($imgArray['tmp_name']);
                return;
            }

            $this->productImages[$imageData['product_id']] = $imageId;

            set_post_thumbnail($post_id, $imageId);
        }
    }

    private function setProductPrices($offer, $post_id)
    {
        $price = (float) $offer['price_value'];

        if (!empty($options['margin'])) {
            if ($options['marginType'] == 'sum') {
                $price += $options['margin'];
            } elseif ($options['marginType'] == 'percent') {
                $price = $price * ($options['margin'] / 100);
            }
        }

        $price = round($price, 2);

        if ($price != 0) {
            update_post_meta($post_id, '_price', $price);
        }
    }

    private function getPreviewImage($categories, $offer): array
    {
        if (!empty($this->productImages[(int) $offer['product_id']])) {
            return [
                'product_id' => (int) $offer['product_id'],
                'imageId' => $this->productImages[(int) $offer['product_id']]
            ];
        }

        foreach ($categories as $category) {
            if (((int) $category['id']) === ((int) $offer['category_id'])) {
                foreach ($category['product'] as $product) {
                    if (((int) $product['id']) === ((int) $offer['product_id'])) {
                        return [
                            'product_id' => (int) $offer['product_id'],
                            'imageUrl' => 'https://accfarm.com/storage/app/media' . $product['preview_image']
                        ];
                    }
                }
            }
        }

        return [];
    }

    /**
     * @throws Exception
     */
    private function getPostData(array $params): array
    {
        $data = [];

        foreach ($params as $param => $option) {
            if ($option == 'required' && empty($_POST[$param])) {
                throw new Exception('Bad Request');
            }

            if (!empty($_POST[$param])) {
                $data[$param] = $_POST[$param];
            } else {
                $data[$param] = false;
            }
        }

        return $data;
    }

    private function callback(Closure $callable)
    {
        try {
            return call_user_func($callable);
        } catch (Exception $exception) {
            wp_send_json_error(['error' => $exception->getMessage()], 400);
        }

        return null;
    }
}