jQuery( function( $ ) {
	"use strict";
	$( "#listing-plans tr" ).click( function() {
		$( this ).find( "input[type=radio]" ).prop( "checked", true );
	} );
	$( ".app-slick-slider" ).slick( {
		infinite: true,
		slidesToShow: 6,
		slidesToScroll: 1,
		autoplay: true,
		autoplaySpeed: 2e3,
		responsive: [ {
			breakpoint: 1024,
			settings: {
				slidesToShow: 3,
				slidesToScroll: 3
			}
		}, {
			breakpoint: 600,
			settings: {
				slidesToShow: 3,
				slidesToScroll: 2,
				arrows: false
			}
		}, {
			breakpoint: 300,
			settings: "unslick"
		} ]
	} );
	$( "#header .menu-primary" ).tinyNav( {
		active: "current_page_item",
		header: clipper_params.text_mobile_navigation
	} );
} );

jQuery( document ).ready( function() {
	jQuery( "#clpr_expire_date" ).datepicker( {
		dateFormat: "yy-mm-dd",
		minDate: 0
	} );
	if ( jQuery.isFunction( jQuery.fn.validate ) ) {
		jQuery( "#loginForm, #commentForm" ).validate( {
			ignore: ".ignore",
			errorClass: "invalid",
			errorElement: "div",
			rules: {
				post_content: {
					minlength: 15
				}
			}
		} );
	}
	jQuery( document ).bind( "cbox_open", function() {
		jQuery( "object, embed, iframe" ).css( {
			visibility: "hidden"
		} );
	} );
	jQuery( document ).bind( "cbox_closed", function() {
		jQuery( "object, embed, iframe" ).css( {
			visibility: "inherit"
		} );
	} );
	if ( jQuery.isFunction( jQuery.fn.jCarouselLite ) ) {
		jQuery( ".slide-contain" ).jCarouselLite( {
			btnNext: ".next",
			btnPrev: ".prev",
			visible: jQuery( window ).width() <= 1024 ? 4 : 5,
			pause: true,
			auto: true,
			timeout: 2800,
			speed: 1100,
			easing: "easeOutQuint"
		} );
		jQuery( ".store-widget-slider" ).jCarouselLite( {
			vertical: true,
			visible: 2,
			pause: true,
			auto: true,
			timeout: 2800,
			speed: 1100
		} );
	}
	if ( clipper_params.direct_links == "1" && ( jQuery.isFunction( Clipboard ) || !clipper_params.coupon_code_hide ) ) {
		var client = new Clipboard( ".coupon_type-coupon-code a.coupon-code-link" );
		client.on( "success", function( event ) {
			jQuery( event.trigger ).fadeOut( "fast" ).html( "<span>" + event.text + '</span><i class="fa fa-scissors" aria-hidden="true"></i>' ).fadeIn( "fast" );
		} );
	} else if ( jQuery.isFunction( jQuery.colorbox ) ) {
		jQuery( "#cboxOverlay" ).append( "<i id='clpr_spinner' class='fa fa-refresh fa-spin' style='display:none;font-size:48px;position:fixed; top:50%; left:50%;'></i>" );
		jQuery( document ).on( "click", ".coupon_type-coupon-code a.coupon-code-link", function() {
			var couponcode = jQuery( this ).data( "clipboard-text" );
			var linkID = jQuery( this ).attr( "id" );
			jQuery( this ).fadeOut( "fast" ).html( "<span>" + couponcode + '</span><i class="fa fa-scissors" aria-hidden="true"></i>' ).fadeIn( "fast" );
			jQuery( this ).parent().next().hide();
			jQuery.colorbox( {
				href: clipper_params.ajax_url + "?action=coupon-code-popup&id=" + linkID,
				transition: "fade",
				maxWidth: "100%",
				trapFocus: false,
				onLoad: function() {
					if ( clipper_params.is_mobile ) {
						jQuery( "#cboxOverlay, #wrapper" ).hide();
					}
				},
				onOpen: function() {
					jQuery( "#clpr_spinner" ).show();
					jQuery( "#cboxWrapper" ).hide();
				},
				onComplete: function() {
					jQuery( "#clpr_spinner" ).hide();
					jQuery( "#cboxWrapper" ).show();
					var clip = new Clipboard( "button#copy-button" );
					clip.on( "success", function( event ) {
						jQuery( "button#copy-button" ).html( clipper_params.text_copied );
						jQuery( ".coupon-code-popup .popup-code-info a" ).fadeOut().addClass( "btn" ).fadeIn();
					} );
					clip.on( "error", function( event ) {
						jQuery( "button#copy-button" ).remove();
						jQuery( ".coupon-code-popup" ).addClass( "clipboard-error" );
					} );
				},
				onCleanup: function() {
					if ( clipper_params.is_mobile ) {
						jQuery( "#wrapper" ).show();
					}
				}
			} );
			return false;
		} );
	}
	if ( jQuery.isFunction( jQuery.colorbox ) ) {
		jQuery( document ).on( "click", "a.mini-comments", function() {
			var postID = jQuery( this ).data( "rel" );
			jQuery.colorbox( {
				href: clipper_params.ajax_url + "?action=comment-form&id=" + postID,
				rel: function() {
					return jQuery( this ).data( "rel" );
				},
				transition: "fade"
			} );
			return false;
		} );
	}
	jQuery( document ).on( "click", "a.show-comments", function() {
		var postID = jQuery( this ).data( "rel" );
		jQuery( "#comments-" + postID ).slideToggle( 400, "easeOutBack" );
		return false;
	} );
	jQuery( document ).on( "click", "a.share", function() {
		jQuery( this ).next( ".drop" ).slideToggle( 400, "easeOutBack" );
		return false;
	} );
	jQuery( ".reports_form_link a" ).on( "click", function() {
		jQuery( this ).parents( "li" ).next().children( ".reports_form" ).slideToggle( 400, "easeOutBack" );
		return false;
	} );
} );

function clearAndColor( el, e2 ) {
	if ( el.defaultValue == el.value ) el.value = "";
	if ( el.style ) el.style.color = "#333";
}

function reText( el ) {
	if ( el.style ) el.style.color = "#ccc";
	if ( el.value == "" ) el.value = el.defaultValue;
}

jQuery( function() {
	jQuery( "#search-field" ).autocomplete( {
		source: function( request, response ) {
			jQuery.ajax( {
				type: "GET",
				url: clipper_params.ajax_url,
				dataType: "json",
				data: {
					action: "ajax-tag-search-front",
					term: request.term
				},
				error: function( XMLHttpRequest, textStatus, errorThrown ) {
					response( [] );
				},
				success: function( data ) {
					if ( data.success == true ) {
						response( jQuery.map( data.items, function( item ) {
							return {
								term: item,
								value: jQuery( "<div />" ).html( item.name ).text()
							};
						} ) );
					} else {
						response( [] );
					}
				}
			} );
		},
		minLength: 2,
		select: function( event, ui ) {
			storeurl = ui.item.term.clpr_store_url;
			if ( storeurl != 0 ) {
				jQuery( ".clpr_store_url" ).html( '<a href="' + storeurl + '" target="_blank">' + storeurl + '<br /><img src="' + ui.item.term.clpr_store_image_url + '" class="screen-thumb" /></a><input type="hidden" name="clpr_store_id" value="' + ui.item.term.term_id + '" /><input type="hidden" name="clpr_store_slug" value="' + ui.item.term.slug + '" />' );
			}
		}
	} );
	jQuery( ".newtag" ).keydown( function( event ) {
		if ( jQuery( "#clpr_store_url" ).length == 0 ) {
			jQuery( ".clpr_store_url" ).html( '<input type="url" class="text" id="clpr_store_url" name="clpr_store_url" value="http://" />' );
		}
	} );
	jQuery( document ).on( "click", "button.comment-submit", function() {
		var comment_post_ID = jQuery( this ).next().val();
		var postURL = clipper_params.ajax_url + "?action=post-comment";
		var author = jQuery( "input#author-" + comment_post_ID ).val();
		var email = jQuery( "#email-" + comment_post_ID ).val();
		var url = jQuery( "#url-" + comment_post_ID ).val();
		var comment = jQuery( "#comment-" + comment_post_ID ).val();
		var postData = "author=" + author + "&email=" + email + "&url=" + url + "&comment=" + comment + "&comment_post_ID=" + comment_post_ID;
		jQuery.ajax( {
			beforeSend: function() {
				validated = jQuery( "#commentform-" + comment_post_ID ).validate( {
					errorClass: "invalid",
					errorElement: "div"
				} ).form();
				jQuery.colorbox.resize();
				return validated;
			},
			type: "POST",
			data: postData,
			url: postURL,
			dataType: "json",
			error: function( XMLHttpRequest, textStatus, errorThrown ) {},
			success: function( data ) {
				if ( data.success == true ) {
					jQuery.colorbox.close();
					if ( jQuery( "#comments-" + comment_post_ID + " .comments-mini" ).length == 0 ) {
						jQuery( "#comments-" + comment_post_ID ).append( "<div class='comments-box coupon'><ul class='comments-mini'>" + data.comment + "</ul></div>" ).fadeOut( "slow" ).fadeIn( "slow" );
					} else {
						jQuery( "#comments-" + comment_post_ID + " .comments-mini" ).prepend( data.comment ).fadeOut( "slow" ).fadeIn( "slow" );
					}
					setTimeout( function() {
						jQuery( "#post-" + comment_post_ID + " a.show-comments span" ).html( data.count ).fadeOut( "slow" ).fadeIn( "slow" );
					}, 2e3 );
				} else {
					jQuery( ".comment-form .post-box" ).html( '<div class="head"><h3>Error</h3></div><div class="text-box"><p>' + data.message + "</p></div>" );
					jQuery.colorbox.resize();
				}
			}
		} );
		return false;
	} );
} );

function thumbsVote( postID, userID, elementID, voteVal, afterVote ) {
	var postData = "vid=" + voteVal + "&uid=" + userID + "&pid=" + postID;
	var theTarget = document.getElementById( elementID );
	jQuery.ajax( {
		target: theTarget,
		type: "POST",
		beforeSend: function() {
			jQuery( "#loading-" + postID ).fadeIn( "fast" );
			jQuery( "#ajax-" + postID ).fadeOut( "fast" );
		},
		data: postData,
		url: clipper_params.ajax_url + "?action=ajax-thumbsup",
		error: function( XMLHttpRequest, textStatus, errorThrown ) {
			alert( "Error: " + errorThrown + " - " + textStatus + " - " + XMLHttpRequest );
		},
		success: function( data, statusText ) {
			theTarget.innerHTML = afterVote;
			jQuery( "#post-" + postID + " span.percent" ).html( data ).fadeOut( "slow" ).fadeIn( "slow" );
		}
	} );
	return false;
}

function resetVotes( postID, elementID, afterReset ) {
	var postData = "pid=" + postID;
	var theTarget = document.getElementById( elementID );
	jQuery.ajax( {
		target: theTarget,
		type: "POST",
		data: postData,
		url: clipper_params.ajax_url + "?action=ajax-resetvotes",
		error: function( XMLHttpRequest, textStatus, errorThrown ) {
			alert( "Error: " + errorThrown + " - " + textStatus + " - " + XMLHttpRequest );
		},
		success: function( data, statusText ) {
			theTarget.innerHTML = afterReset;
		}
	} );
	return false;
}

jQuery( document ).foundation();
