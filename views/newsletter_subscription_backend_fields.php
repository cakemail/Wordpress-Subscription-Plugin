            <?php foreach ( $instance['selected_list']->fields as $field => $values ) { ?>
                <?php
                    $show = $values['show'];
                    $label = $values['label'];
                ?>
                <?php if ( $field != 'id' && $field != 'registered' ) { ?>
                    <li class="<?php echo !$show && $field != 'email' ? 'drag-disabled' : ''; ?>">
                        <img class="drag-icon" src="<?php echo plugins_url('newsletter-subscription-form/img/icon_move.png'); ?>" />
                        <input class="" id="<?php echo $this->get_field_id( 'field-chk-'.$field ); ?>" name="<?php echo $this->get_field_name( 'field-chk-'.$field ); ?>" type="checkbox" <?php echo $show || $field == 'email' ? 'checked="checked"' : ""; ?> <?php echo $field == 'email' ? 'disabled="disabled"' : ""; ?>/>
                        <input id="<?php echo $this->get_field_id( 'field-chk-index-'.$field ); ?>" name="<?php echo $this->get_field_name( 'field-chk-index-'.$field ); ?>" type="hidden" />
                        <label class="" for="<?php echo $this->get_field_id( 'field-chk-'.$field ); ?>"><?php echo $field; ?></label>
                        <input class="" id="<?php echo $this->get_field_id( 'field-'.$field ); ?>" name="<?php echo $this->get_field_name( 'field-'.$field ); ?>" type="text" placeholder="<?php echo __('Title','newsletter-subscription-widget'); ?>: <?php echo $field; ?>" value="<?php echo $label ?>" />
                    </li>
                <?php } ?>
            <?php } ?>
