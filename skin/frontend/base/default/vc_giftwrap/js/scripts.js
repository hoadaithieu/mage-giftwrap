var GiftWrap = {
	defaultAction: function() {
		
		
		jQuery('[type="radio"][name^="giftwrap_group"]').click(function() {
			jQuery('[type="radio"][name^="giftwrap_box"],[type="radio"][name^="giftwrap_card"]').each(function() {
				jQuery(this).attr("checked", false);
			});
		});
		
		jQuery('[type="radio"][name^="giftwrap_box"],[type="radio"][name^="giftwrap_card"]').click(function() {
			jQuery('[type="radio"][name^="giftwrap_group"]').each(function() {
				jQuery(this).attr("checked", false);
			});
			
			if (jQuery(this).attr('name').match(/giftwrap_card/)) {
				jQuery('#card_max_character').html(jQuery(this).attr('data-max-characters'));
				jQuery('#card_message').attr('maxlength', jQuery(this).attr('data-max-characters'));
			}
		});
		
		jQuery('[type="radio"][name^="giftwrap_existing"]').change(function() {
			_val = jQuery(this).val();
			jQuery('.vcgw-group').hide();
			jQuery('.vcgw-box').hide();
			jQuery('.vcgw-card').hide();
			
			if (jQuery(this).is(":checked")) {
				if (_val == 1) {
					jQuery('.vcgw-group').show();
					jQuery('[type="radio"][name^="giftwrap_box"],[type="radio"][name^="giftwrap_card"]').each(function() {
						jQuery(this).attr("checked", false);
					});
					
				} else {
					jQuery('.vcgw-box').show();
					jQuery('.vcgw-card').show();
					jQuery('[type="radio"][name^="giftwrap_group"]').each(function() {
						jQuery(this).attr("checked", false);
					});
				}
			} 
		});
		
		if (!jQuery('#giftwrap_existing_1').length) {
			jQuery('#giftwrap_existing_0').click();
		} else {
			jQuery('#giftwrap_existing_1').click();
		}
	},
	
	updateAction: function() {
		jQuery('.vcgw-top-tool span.close').unbind('click').click(function() {
			jQuery('#giftwrap_overlay').hide();
		});
		
		jQuery('#giftwrap_overlay .button-change').unbind('click').click(function() {
			_isError = true;
			
			jQuery('[type="radio"][name^="giftwrap_group"],[type="radio"][name^="giftwrap_box"]').each(function() {
				if (jQuery(this).is(":checked")) {
					_isError = false;
					return;
				}
			});
			
			if (_isError) {
				alert(jQuery(this).attr('data-error-msg'));
				return ;
			}
			
			
			
			jQuery('#giftwrap_bg_overlay').show();
			_link = jQuery(this).attr('data-link');
			data = jQuery('#wrapForm').serialize();
			jQuery.ajax({
				type: 'POST',
				dataType: 'json',
				url: _link,
				data: data
			}).done(function(result) {
				if (result.code == 'success') {
					document.location = result.link;
				}
				jQuery('#giftwrap_bg_overlay').hide();
			}).error(function(result) {
				if (result.msg) {
					jQuery('#giftwrap_overlay .error').show().html(result.msg);
				}
				jQuery('#giftwrap_bg_overlay').hide();
			});	
			
		});
		
		
		this.defaultAction();		
	}
}

jQuery(function() {

	jQuery('[name^="giftwrap_qty"]').focus(function() {
		jQuery('#update_giftwrap_action_' + jQuery(this).attr('data-id')).show();
	});

	
	jQuery('[name="empty_giftwrap"]').click(function() {
		_link = jQuery(this).attr('data-link');													  
		document.location = _link;													  	
	});
	
	jQuery('[name^="update_giftwrap_action"]').click(function() {
		_link = jQuery(this).attr('data-link') + 'qty/' + jQuery('#giftwrap_qty_' + jQuery(this).attr('data-id')).val();													  
		document.location = _link;													  	
	});
	
	jQuery('[id^="giftwrap_box"],[id^="giftwrap_card"],[id^="giftwrap_product"],[id^="giftwrap_checkout_item"]').click(function() {
		if (jQuery(this).attr('id').match(/giftwrap_checkout_item/)) {
			_wrapped = jQuery(this).attr('data-wrapped');
			if (_wrapped == 1) {
				_link = jQuery(this).attr('data-delete-link');
				document.location = _link;
				return;
			}
		}
		_link = jQuery(this).attr('data-link');
		data = {};
		jQuery('#giftwrap_bg_overlay').show();
		jQuery.ajax({
			type: 'POST',
			dataType: 'json',
			url: _link,
			data: data
		}).done(function(result) {
			if (result.code == 'success') {
				jQuery('#giftwrap_overlay').html(result.content);
			}
			jQuery('#giftwrap_overlay').center().show();
			GiftWrap.updateAction();
			jQuery('#giftwrap_bg_overlay').hide();
		}).error(function(result) {
			if (result.msg) {
				alert(result.msg);
			}
			jQuery('#giftwrap_overlay').center().show();
			jQuery('#giftwrap_bg_overlay').hide();
		});	
							
	});

	if (!jQuery('#giftwrap_overlay').length) {
		jQuery(document.body).append('<div id="giftwrap_overlay" class="giftwrap_overlay" style="display:none"></div><div id="giftwrap_bg_overlay" class="giftwrap_bg_overlay" style="display:none"></div>');
	}
	
	GiftWrap.defaultAction();
});




jQuery.fn.center = function () {
    this.css("position","absolute");
    this.css("top", Math.max(0, ((jQuery(window).height() - jQuery(this).outerHeight()) / 2) + jQuery(window).scrollTop()) + "px");
    this.css("left", Math.max(0, ((jQuery(window).width() - jQuery(this).outerWidth()) / 2) + jQuery(window).scrollLeft()) + "px");
    return this;
}

jQuery(window).resize(function(){
	jQuery('#giftwrap_overlay').center();							   
});
