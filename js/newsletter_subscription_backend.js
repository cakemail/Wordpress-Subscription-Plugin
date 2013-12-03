Newsletter.Subscription = { }; 

Newsletter.Subscription.Widget = function( el ) {
    var me = this;

    var constructor = function(){
        me.jq = jQuery('#'+el);
        me.index = getInstanceIndex();

        registerEvents();
        setFieldOrder();
    }
    var getInstanceIndex = function() {
        return el.substring(el.lastIndexOf('-')+1);
    }

    var registerEvents = function() {
        if(!me.jq.find('select.opt-list').data('events'))
            me.jq.find('select.opt-list').change(getFields); 

        if(!me.jq.find('.fields-container').data('events')) {
            me.jq.find('.fields-container').sortable({
                handle: 'img.drag-icon',
                cancel: '.drag-disabled',
                items:  'li:not(.drag-disabled)',
                opacity: 0.6, 
                update: setFieldOrder
            });
            me.jq.find('.fields-container').disableSelection();
        }

        if(!me.jq.find('.fields-container input[type="checkbox"]').data('events'))
            me.jq.find('.fields-container input[type="checkbox"]').change(changeFieldState);

        if(!me.jq.find('.section.collapsable .title').data('events'))
            me.jq.find('.section.collapsable .title').click(function() {
                var title = jQuery(this);
                var form_container = title.parent().find('.form');
                title.toggleClass('open');
                form_container.toggleClass('hidden');
                jQuery(get_field_id('is_' + this.id + "_open")).val(form_container.is(":visible")?1:0);
            })
    };

    var getFields = function() {
        var fields_container = me.jq.find('.fields-container');

        fields_container.hide('blind');

        Newsletter.call_server('get_fields', {'list_id' : this.value, 'widget_index' : me.index}, function( response ){
            fields_container.html(response);
            fields_container.show('blind');
            registerEvents();
            setFieldOrder();
        });
    }

    var changeFieldState = function() {
        jQuery(this).parent().toggleClass('drag-disabled');
    }

    var setFieldOrder = function( ) {
        jQuery('.fields-container').each(function(){
            jQuery(this).children().each(function() {
                var item = jQuery(this);
                var id = item.find('input[type="checkbox"]').attr('id');

                item.find('#'+id.replace('chk-', 'chk-index-')).val(jQuery(this).index());
            });
        });
        me.jq.find(get_field_id('fields-sorted')).val(1); 
    }

    var get_field_id = function( $name ) {
        return "#widget-newslettersubscriptionform-" + me.index + "-" + $name;
    }

    constructor();
};
