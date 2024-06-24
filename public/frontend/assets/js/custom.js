document.addEventListener("DOMContentLoaded", () => {
    // var otpInputs = document.querySelectorAll('otp-input');

    // otpInputs.forEach((element, index) => {
    //     console.log('here' + index);
    //     element.addEventListener("keyup", ()=> {
    //         if (element.value.length === element.maxLength && index < otpInputs.length - 1) {
    //             otpInputsArray[index + 1].focus();
    //         }
    //     });
    // });
    
    var homeBannerSwiper = new Swiper(".home-banner-slider", {
        slidesPerView: 1,
        spaceBetween: 0,
        // effect: 'fade',
        loop: true,
        // autoplay: {
        //     delay: 5000,
        //     disableOnInteraction: false,
        // },
        // navigation: {
        //     nextEl: ".banner-swiper-button-next",
        //     prevEl: ".banner-swiper-button-prev",
        // },
        pagination: {
            el: ".banner-swiper-pagination",
            clickable: true
        },
    });
    
    var subCat1Swiper = new Swiper(".sub-cat-slider-1", {
        slidesPerView: 'auto',
        spaceBetween: 18,
        navigation: {
            nextEl: ".sub-cat-1-swiper-button-next",
            prevEl: ".sub-cat-1-swiper-button-prev",
        },
    });
    
    var subCat1Swiper = new Swiper(".sub-cat-slider-2", {
        slidesPerView: 'auto',
        spaceBetween: 18,
        navigation: {
            nextEl: ".sub-cat-2-swiper-button-next",
            prevEl: ".sub-cat-2-swiper-button-prev",
        },
    });
    
    var homeBannerSwiper = new Swiper(".pop-cat-slider", {
        slidesPerView: 4,
        spaceBetween: 18,
        navigation: {
            nextEl: ".pop-cat-swiper-button-next",
            prevEl: ".pop-cat-swiper-button-prev",
        },
        breakpoints: {
            320: {
                slidesPerView: 1,
            },
            576: {
                slidesPerView: 2,
            },
            992: {
                slidesPerView: 3,
            },
            1199: {
                slidesPerView: 4,
            },
        }
    });

    var tutorialsSlider = new Swiper(".tutorials-slider", {
        slidesPerView: 'auto',
        spaceBetween: 28,
        // loop: true,
        // autoplay: {
        //     delay: 2500,
        //     disableOnInteraction: false,
        // },
        // pagination: {
        //     el: ".tutorials-swiper-pagination",
        //     clickable: true
        // },
        navigation: {
            nextEl: ".tutorial-swiper-button-next",
            prevEl: ".tutorial-swiper-button-prev",
        },
    });


    var brandsSwiper = new Swiper(".brands-slider", {
        slidesPerView: 'auto',
        spaceBetween: 80,
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        // breakpoints: {
        //     320: {
        //         slidesPerView: 2,
        //     },
        //     768: {
        //         slidesPerView: 4,
        //     },
        //     992: {
        //         slidesPerView: 5,
        //     },
        // }
    });

    var customerSayingSwiper = new Swiper(".customer-saying-slider", {
        slidesPerView: 1,
        spaceBetween: 0,
        navigation: {
            nextEl: ".customer-swiper-button-next",
            prevEl: ".customer-swiper-button-prev",
        },
    });

    $("input[name='sendwatchlist']").click(function () {
        var inputval = $(this).val();
        if (inputval == "sendwatchlistgroup") {
            $("#watchlistoptions").addClass('show');
            $("#single_watchlist_div").hide();
        } else {
            $("#watchlistoptions").removeClass('show');
            $("#single_watchlist_div").show();
        }
    });

    $("input[name='sendinquirylist']").click(function () {
        var inputval = $(this).val();
        if (inputval == "sendinquiryexisting") {
            $("#inquiryoptions").addClass('show');
        } else {
            $("#inquiryoptions").removeClass('show');
        }
    });
    $("input[name='inquiry_type']").click(function () {
        var inputval = $(this).val();
        if (inputval == "existing-inquiry") {
            $("#inquiryoptions").addClass('show');
        } else {
            $("#inquiryoptions").removeClass('show');
        }
    });


    // $("input[name='auctionfrom']").click(function () {
    //     var inputval = $(this).val();
    //     if (inputval == "region") {
    //         $("#selectRegion").addClass('show');
    //     } else {
    //         $("#selectRegion").removeClass('show');
    //     }
    // });
    $("input[name='supplier_location']").click(function () {
        var inputval = $(this).val();
        if (inputval == "region") {
            $("#selectRegion").addClass('show');
        } else {
            $("#selectRegion").removeClass('show');
        }
    });

    $(".solid-stars").each(function () {
        var ratingValue = $(this).data("rating");
        var solidStarsWidth = ((Number(ratingValue) / 5) * 100);
        $(this).css("width", solidStarsWidth + "%");
    });

    $("input[name='rateas']").click(function () {
        var inputval = $(this).val();
        if (inputval == 1) {
            $("#ratingInputsBidder").removeClass('show');
            $("#ratingInputsAuctioneer").addClass('show');
        } else if (inputval == 2) {
            $("#ratingInputsAuctioneer").removeClass('show');
            $("#ratingInputsBidder").addClass('show');
        }
    });

    $("input[name='prodserv']").click(function () {
        var inputval = $(this).val();
        if (inputval == "productdetails") {
            $("#serviceInputs").removeClass('show');
            $("#productInputs").addClass('show');
        } else if (inputval == "servicedetails") {
            $("#productInputs").removeClass('show');
            $("#serviceInputs").addClass('show');
        }
    });

    $("input[name='consumption']").click(function () {
        var inputval = $(this).val();
        if (inputval == "daily") {
            $("#yearlyConsumptionInputs").removeClass('show');
            $("#dailyConsumptionInputs").addClass('show');
        } else if (inputval == "yearly") {
            $("#dailyConsumptionInputs").removeClass('show');
            $("#yearlyConsumptionInputs").addClass('show');
        }
    });

    $("#sidebarOpener").on("click", function () {
        $(".sidebar-toggler").hide();
        $("#profileSidebar").addClass("show");
    });

    $("#sidebarClose").on("click", function () {
        $(".sidebar-toggler").show();
        $("#profileSidebar").removeClass("show");
    });

    $("input[name='allotrate']").click(function () {
        var inputval = $(this).val();
        if (inputval == "yes") {
            $("#allotAmount").prop('disabled', true);
        } else if (inputval == "no") {
            $("#allotAmount").prop('disabled', false);
        }
    });

    var currentDate = new Date();

    /*$("#filterStartDate").datepicker({
        defaultDate: currentDate,
        dateFormat: "d M, yy",
        onSelect: function (dateText) {
            $("#filterSelectedStartDate").text(dateText);
        }
    });

    $("#filterSelectedStartDate").text($.datepicker.formatDate("d M, yy", currentDate));

    $("#filterSelectedStartDate").on("click", function () {
        $("#filterStartDate").datepicker("show");
    });

    $("#filterEndDate").datepicker({
        defaultDate: currentDate,
        dateFormat: "d M, yy",
        onSelect: function (dateText) {
            $("#filterSelectedEndDate").text(dateText);
        }
    });

    $("#filterSelectedEndDate").text($.datepicker.formatDate("d M, yy", currentDate));

    $("#filterSelectedEndDate").on("click", function () {
        $("#filterEndDate").datepicker("show");
    });*/

    $(".read-more span").on("click", function () {
        if ($(this).parent().parent().find("p").hasClass("hidden")) {
            $(this).parent().parent().find("p").removeClass("hidden");
            $(this).text("read less");
        } else {
            $(this).parent().parent().find("p").addClass("hidden");
            $(this).text("read more");
        }
    });

    $(".see-more span").on("click", function () {
        if ($(this).parent().parent().find("ul.participant-data-list").hasClass("hidden")) {
            $(this).parent().parent().find("ul.participant-data-list").removeClass("hidden");
            $(this).text("see less");
        } else {
            $(this).parent().parent().find("ul.participant-data-list").addClass("hidden");
            $(this).text("see more");
        }
    });


    var testiBottomSwiper = new Swiper(".testi-bottom", {
        loop: true,
        spaceBetween: 10,
        centeredSlides: true,
        slidesPerView: 3,
        autoplay:true,
        freeMode: true,
        watchSlidesProgress: true,
      });
    var testiTopSwiper = new Swiper(".testi-top", {
        loop: true,
        spaceBetween: 10,
        autoplay:true,
        // navigation: {
        //   nextEl: ".swiper-button-next",
        //   prevEl: ".swiper-button-prev",
        // },
        thumbs: {
          swiper: testiBottomSwiper,
        },
    });



});

function registerOtp() {
    $("#registerOtpModal").modal("show");
}

// Funtion to update rating
function giveRating(element, n) {

    var allChilds = element.parentElement.getElementsByTagName('li');
    console.log(allChilds);
    let i = 0;
    while (i < 5) {
        allChilds[i].className = "star";
        i++;
    }

    for (let i = 0; i < n; i++) {
        if (n == 1) cls = "one";
        else if (n == 2) cls = "two";
        else if (n == 3) cls = "three";
        else if (n == 4) cls = "four";
        else if (n == 5) cls = "five";
        allChilds[i].className = "star " + cls;
    }
}
$("form").submit(function () {
    // Disable the submit button
    $("button[type='submit']").prop("disabled", true);

    // Enable the submit button after 5 seconds
    setTimeout(function () {
        $("button[type='submit']").prop("disabled", false);
    }, 3000); // 5000 milliseconds = 5 seconds
});

$("form :input").change(function () {
    $("button[type='submit']").prop("disabled", false);
});
$("form select").change(function () {
    $("button[type='submit']").prop("disabled", false);
});


// custom autocomplete js

$(document).ready(function() {
    // Sample categories and products with icons
    const items = [
        {
            category: "Restaurants",
            products: [
                { name: "Pizza Hut" },
                { name: "Burger King" },
                { name: "Subway" },
                { name: "KFC" },
                { name: "Domino's Pizza" }
            ]
        },
        {
            category: "Hospitals",
            products: [
                { name: "Apollo Hospital" },
                { name: "Fortis Hospital" },
                { name: "Max Healthcare" },
                { name: "AIIMS" },
                { name: "Medanta" }
            ]
        },
        {
            category: "Schools",
            products: [
                { name: "Delhi Public School" },
                { name: "Ryan International" },
                { name: "Kendriya Vidyalaya" },
                { name: "Springdales" },
                { name: "Amity International" }
            ]
        }
    ];

    let debounceTimer;

    function showSuggestions(query) {
        let queryWords = query.toLowerCase().split(/\s+/).filter(word => word.length > 0);
        $('#autocomplete-suggestions').empty();

        items.forEach(item => {
            let filteredProducts = item.products.filter(product => {
                return queryWords.every(word => product.name.toLowerCase().includes(word));
            });
            if (filteredProducts.length > 0) {
                filteredProducts.forEach(product => {
                    $('#autocomplete-suggestions').append(`
                        <div class="autocomplete-suggestion">
                            <div class="suggestion-icon">
                               <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><clipPath id="a"><path d="M0 0h24v24H0z" fill="#000000" opacity="1" data-original="#000000" class=""></path></clipPath><g fill="#000" fill-rule="evenodd" clip-path="url(#a)" clip-rule="evenodd"><path d="M23.707 5.293a1 1 0 0 1 0 1.414l-9.5 9.5a1 1 0 0 1-1.414 0L8.5 11.914l-6.793 6.793a1 1 0 0 1-1.414-1.414l7.5-7.5a1 1 0 0 1 1.414 0l4.293 4.293 8.793-8.793a1 1 0 0 1 1.414 0z" fill="#000000" opacity="1" data-original="#000000" class=""></path><path d="M16 6a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v6a1 1 0 1 1-2 0V7h-5a1 1 0 0 1-1-1z" fill="#000000" opacity="1" data-original="#000000" class=""></path></g></g></svg>
                            </div>
                            <div class="suggestion-right">
                                <div class="autocomplete-business-name">${product.name}</div>
                                <div class="autocomplete-category-name">${item.category}</div>
                            </div>
                        </div>
                    `);
                });
            }
        });
    }

    $('#autocomplete-input').on('input', function() {
        clearTimeout(debounceTimer);
        let query = $(this).val();

        debounceTimer = setTimeout(() => {
            showSuggestions(query);
        }, 300); // Delay of 300ms to mimic debounce and simulate async request
    });

    $('#autocomplete-input').on('focus', function() {
        showSuggestions('');
    });

    $(document).on('click', '.autocomplete-suggestion', function() {
        $('#autocomplete-input').val($(this).find('.autocomplete-business-name').text());
        $('#autocomplete-suggestions').empty();
    });

    $(document).on('click', function(event) {
        if (!$(event.target).closest('#autocomplete-input').length && !$(event.target).closest('#autocomplete-suggestions').length) {
            $('#autocomplete-suggestions').empty();
        }
    });
});


