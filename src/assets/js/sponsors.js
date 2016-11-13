var $model = $("#exampleModal11");

$(".exampleModal1").on('click', function(e){
    e.preventDefault();

    $("#companyLogo").attr("src", $(this).data("logo"));
    $("#companyDescription").text($(this).data("description"));

    var data = $(this).data();

    var socialMedia = "";

    var index;
    for (index in data) {
        if (data.hasOwnProperty(index)) {
            if (index.indexOf("social") !== -1) {
                switch (index.substring("social".length)){
                    case "Dribbble":
                        socialMedia += buildLink($(this).data(index), "fi-social-dribbble");
                        break;
                    case "Facebook":
                        socialMedia += buildLink($(this).data(index), "fi-social-facebook");
                        break;
                    case "Twitter":
                        socialMedia += buildLink($(this).data(index), "fi-social-twitter");
                        break;
                    case "Youtube":
                        socialMedia += buildLink($(this).data(index), "fi-social-youtube");
                        break;
                    case "Instagram":
                        socialMedia += buildLink($(this).data(index), "fi-social-instagram");
                        break;
                }
            }
        }
    }

    $(".sponsor-social-media").html(socialMedia);

    $(".website").attr("href", $(this).data('website'));

    $model.foundation('open');
});

var buildLink = function(url, fiClass) {
  return "<a href=\"" + url + "\" target=\"_blank\"><i class=\"" + fiClass + "\"></i></a>";
};