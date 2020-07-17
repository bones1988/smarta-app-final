<?php
/**
 * @file
 * Contains \Drupal\block_module\Plugin\Block\OwnerBlock.
 */
namespace Drupal\block_module\Plugin\Block;


use Drupal;
use Drupal\Core\Block\BlockBase;
use PDO;

/**
 * Provides a custom_block.
 *
 * @Block(
 *   id = "block_module",
 *   admin_label = @Translation("Owner info block"),
 *   category = @Translation("Smart app")
 * )
 */
class OwnerBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    //user id
    $user_id = Drupal::currentUser()->id();

    //user name
    $query = \Drupal::database()->select('user__field_first_name', 'fn');
    $query->addField('fn', 'field_first_name_value');
    $query->condition('fn.entity_id', $user_id);
    $user_name = $query->execute()->fetchField();

    //company names
    $query = \Drupal::database()->select('commerce_store_field_data', 'csfd');
    $query->fields('csfd', array('store_id', 'name'));
    $query->condition('csfd.uid', $user_id);
    $companies = $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    $companies_markup = '';
    foreach ($companies as $company) {
      $companies_markup =  $company['name'] . ' <a href="/store/' . $company['store_id'] . '/edit">edit</a></li>';
    }
    if (empty($companies)) {
      $companies_markup = ' You did not create any company';
    }

    //count products
    $query = \Drupal::database()->select('commerce_product_field_data', 'cpfd');
    $query->condition('cpfd.uid', $user_id);
    $products_count = $query->countQuery()->execute()->fetchField();
    $products_link = ' <a href="/users_products">see</a></p>';
    if($products_count<1){
      $products_link = '';
    }

    return array(
      '#markup' => '<p>Your name: ' . $user_name  . '<a href="/user/' . $user_id . '/edit"> edit</a></p>' .
     '<p>Organizations: ' . $companies_markup .'</p>' .

      '</ul>' .
        '<p>Count products: ' . $products_count . $products_link
    );
  }

  public function getCacheMaxAge() {
    return 0;
  }
}
