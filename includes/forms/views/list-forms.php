<?php
defined( 'ABSPATH' ) or exit;

/**
 * This file displays the list of all available forms
 */
?>

<div class="wrap mb4wp-wrap">
    <h1 class="wp-heading-inline"><?php _e( 'Mail Blaze Forms', 'mailblaze-for-wp' ); ?></h1>
    <a href="<?php echo mb4wp_get_add_form_url(); ?>" class="page-title-action"><?php _e( 'Add New', 'mailblaze-for-wp' ); ?></a>
    
    <hr class="wp-header-end">
    
    <div class="mb4wp-notices">
        <?php $this->messages->show(); ?>
    </div>    <style>
        .mb4wp-forms-table td input[readonly] {
            background: #f8f8f8;
            font-size: 12px;
        }
        .mb4wp-forms-table td input[readonly]:focus {
            border-color: #ddd;
            box-shadow: none;
        }
        .mb4wp-forms-table th {
            font-weight: 600;
        }
        .mb4wp-forms-table .row-actions {
            position: inherit;
        }
        .mb4wp-forms-table .delete-link {
            color: #a00;
        }
        .mb4wp-forms-table .delete-link:hover {
            color: #dc3232;
            border-color: #a00;
        }
    </style>

    <?php if ( empty( $forms ) ) : ?>
        <p><?php _e( 'No forms found. Use the button above to create your first form.', 'mailblaze-for-wp' ); ?></p>
    <?php else: ?>
        <table class="wp-list-table widefat fixed striped mb4wp-forms-table">            <thead>
                <tr>
                    <th scope="col" class="column-primary"><?php _e( 'Name', 'mailblaze-for-wp' ); ?></th>
                    <th scope="col"><?php _e( 'Shortcode', 'mailblaze-for-wp' ); ?></th>
                    <th scope="col"><?php _e( 'Lists', 'mailblaze-for-wp' ); ?></th>
                    <th scope="col"><?php _e( 'Actions', 'mailblaze-for-wp' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach( $forms as $form ): ?>
                    <?php
                        $lists = array();
                        foreach ( $form->settings['lists'] as $list_id ) {
                            $list = $mailblaze->get_list( $list_id, true ); // Force fetch from API if not in cache
                            if ( $list ) {
                                // Only add lists with proper names (not "Unknown List")
                                if ($list->name !== 'Unknown List') {
                                    $lists[] = $list->name;
                                }
                            }
                        }
                    ?>
                    <tr>
                        <td class="column-primary">
                            <strong>
                                <?php echo esc_html($form->name); ?>
                            </strong>
                        </td>
                        <td>
                            <input type="text" onfocus="this.select();" readonly="readonly" value="[mb4wp_form id=&quot;<?php echo $form->ID; ?>&quot;]" class="widefat" />
                        </td>
                        <td>
                            <?php
                            if (!empty($lists)) {
                                echo esc_html(implode(', ', $lists));
                            } else {
                                // If no valid lists were found
                                echo '<em>' . esc_html__('No lists found', 'mailblaze-for-wp') . '</em>';
                            }
                            ?>
                        </td>
                        <td>
                            <a href="<?php echo mb4wp_get_edit_form_url( $form->ID ); ?>"><?php _e( 'Edit', 'mailblaze-for-wp' ); ?></a> | 
                            <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=mailblaze-for-wp-forms&view=delete-form&form_id=' . $form->ID ), 'mb4wp_delete_form' ); ?>" class="delete-link" onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to delete this form?', 'mailblaze-for-wp' ); ?>');"><?php _e( 'Delete', 'mailblaze-for-wp' ); ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
