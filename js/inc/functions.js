/*----------------------------------------------------------
    Detect Mobile Browser
-----------------------------------------------------------*/
if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
   $('html').addClass('ismobile');
}

$(window).load(function () {
    /*----------------------------------------------------------
        Page Loader (no longer used)
     -----------------------------------------------------------*/
    if(!$('html').hasClass('ismobile')) {
        if($('.page-loader')[0]) {
            setTimeout (function () {
                $('.page-loader').fadeOut();
            }, 500);

        }
    }
	
	// have the navbar sticky when the page is scrolled past it
	$(document).scroll(function() {
		var scrollPosition = $(document).scrollTop();
		
		if(scrollPosition > $('.company-header').height()) {
			$('body').addClass('is-fixed');
		} else if($('body').hasClass('is-fixed')) {
			$('body').removeClass('is-fixed');
		}
		//alert($(document).scrollTop());
	});
	
})

$(document).ready(function(){
	
	/*----------------------------------------------------------
        Scrollbar
    -----------------------------------------------------------*/
    function scrollBar(selector, theme, mousewheelaxis) {
        $(selector).mCustomScrollbar({
            theme: theme,
            scrollInertia: 100,
            axis:'yx',
            mouseWheel: {
                enable: true,
                axis: mousewheelaxis,
                preventDefault: true
            }
        });
    }

    if (!$('html').hasClass('ismobile')) {
        //On Custom Class
        if ($('.c-overflow')[0]) {
            scrollBar('.c-overflow', 'minimal-dark', 'y');
        }
    }
	
   
    /*----------------------------------------------------------
        Bootstrap Accordion Fix
    -----------------------------------------------------------*/
    if ($('.collapse')[0]) {

        //Add active class for opened items
        $('.collapse').on('show.bs.collapse', function (e) {
            $(this).closest('.panel').find('.panel-heading').addClass('active');
        });

        $('.collapse').on('hide.bs.collapse', function (e) {
            $(this).closest('.panel').find('.panel-heading').removeClass('active');
        });

        //Add active class for pre opened items
        $('.collapse.in').each(function(){
            $(this).closest('.panel').find('.panel-heading').addClass('active');
        });
    }


    /*----------------------------------------------------------
        Text Field
    -----------------------------------------------------------*/
    //Add blue animated border and remove with condition when focus and blur
	if($('.fg-line')[0]) {
	    $('body').on('change', '.fg-line .form-control', function(){
			var i = $(this).val();

			if (!i.length == 0) $(this).closest('.fg-line').addClass('fg-toggled');
        })

        $('body').on('focus', '.fg-line .form-control', function(){
            $(this).closest('.fg-line').addClass('fg-toggled');
		})

        $('body').on('blur', '.form-control', function(){
            var p = $(this).closest('.form-group, .input-group');
            var i = p.find('.form-control').val();

            if (p.hasClass('fg-float')) {
                if (i.length == 0) {
                    $(this).closest('.fg-line').removeClass('fg-toggled');
                }
            }
            else {
                $(this).closest('.fg-line').removeClass('fg-toggled');
            }
        });
    }

    //Add blue border for pre-valued fg-float text feilds
    if($('.fg-float')[0]) {
		setTimeout(function() {
			$('.fg-float .form-control').each(function(){
				var i = $(this).val();

				if (!i.length == 0) {
					$(this).closest('.fg-line').addClass('fg-toggled');
				}

			});
		}, 100);
    }

	/*----------------------------------------------------------
        Data tables
    -----------------------------------------------------------*/
	$('.data-table').DataTable({
		"searching": false,
		"ordering":  false,
		"paging":  false,
		"info":  false,
		"lengthChange": false
		//"dom": 'rtilp'
	});


    /*-----------------------------------------------------------
        Link prevent
    -----------------------------------------------------------*/
    $('body').on('click', '.a-prevent', function(e){
        e.preventDefault();
    });

	/*-----------------------------------------------------------
        create layer elements  (no longer used)
    -----------------------------------------------------------*/
	function createFeatureItem(header, amount, percentage) {
		// create the element (from the templates)
		var featureItem = $('#templates .mini-charts-item').clone();
		
		// apply the values
		$('.header', featureItem).html(header);
		$('.amount', featureItem).html(amount);
		
		var absPercentage = Math.abs(percentage);
		if(percentage < 0) {
			// negative value
			featureItem.addClass('bgm-red negative');
			$('.percentage', featureItem).html('('+absPercentage+'%)');
			$('.movement .zmdi', featureItem).addClass('zmdi-triangle-down');
			
		} else {
			featureItem.addClass('bgm-lightgreen');
			$('.percentage', featureItem).html(percentage+'%');
			$('.movement .zmdi', featureItem).addClass('zmdi-triangle-up');
			
		}
		
		if(absPercentage < 10) featureItem.addClass('lt10');
		else if(absPercentage < 100) featureItem.addClass('lt100');
		
		// temp - add the item to the current tab
		$('.tab-pane.active').append(featureItem);
		
		return featureItem;
    }
	
	/*-----------------------------------------------------------
        Waves
    -----------------------------------------------------------*/
    (function(){
         Waves.attach('.btn');
         Waves.attach('.btn-icon, .btn-float', ['waves-circle', 'waves-float']);
        Waves.init();
    })();
	
	/*----------------------------------------------------------
        TEMP - plot graph on dashboard
    -----------------------------------------------------------*/
	plotGraph('dashboard', '');
	
	/*----------------------------------------------------------
        WYSIWYG
    -----------------------------------------------------------*/
	tinymce.init({
	  selector: 'textarea.editable',
	  inline: false,
	  plugins: 'lists advlist textcolor ',
	  toolbar: 'bold italic underline forecolor | bullist numlist outdent indent',
	  menubar: false
	});
	//toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
	
});

/*----------------------------------------------------------
        Bootstrap Accordion Fix
    -----------------------------------------------------------*/
function prepareAccordions(context) {
    if ($('.collapse', context)[0]) {

        //Add active class for opened items
        $('.collapse', context).on('show.bs.collapse', function (e) {
            $(this).closest('.panel').find('.panel-heading').addClass('active');
        });

        $('.collapse', context).on('hide.bs.collapse', function (e) {
            $(this).closest('.panel').find('.panel-heading').removeClass('active');
        });

        //Add active class for pre opened items
        $('.collapse.in', context).each(function(){
            $(this).closest('.panel').find('.panel-heading').addClass('active');
        });
    }
}