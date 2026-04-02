"use strict";

/** Resolve AJAX URL (works with base href edge cases and PHP built-in server). */
function ciAjaxUrl(path) {
  var base = typeof window.CI_BASE_URL !== "undefined" ? window.CI_BASE_URL : "";
  path = String(path || "").replace(/^\//, "");
  return base + path;
}

/** Select2 inside Bootstrap modals needs the modal as parent for correct z-index and focus. */
function select2DropdownParent($el) {
  var $modal = $el.closest(".modal");
  return $modal.length ? $modal : $(document.body);
}

$(document).ready(function () {
  "use strict";
  $(".pos_client").hide();
  $(document.body).on("change", "#pos_select", function () {
    "use strict";
    var v = $("select.pos_select option:selected").val();
    if (v === "add_new") {
      $(".pos_client").show();
    } else {
      $(".pos_client").hide();
    }
  });
  $(".pos_client1").hide();
  $(document.body).on("change", "#pos_select1", function () {
    "use strict";
    var v = $("select.pos_select1 option:selected").val();
    if (v === "add_new") {
      $(".pos_client1").show();
    } else {
      $(".pos_client1").hide();
    }
  });
});

$(document).ready(function () {
  "use strict";

  var patientAjax = {
    url: ciAjaxUrl("patient/getPatientinfoWithAddNewOption"),
    type: "post",
    dataType: "json",
    delay: 250,
    data: function (params) {
      return {
        searchTerm: params.term,
      };
    },
    processResults: function (response) {
      return {
        results: response,
      };
    },
    cache: true,
  };

  var doctorAjax = {
    url: ciAjaxUrl("doctor/getDoctorInfo"),
    type: "post",
    dataType: "json",
    delay: 250,
    data: function (params) {
      return {
        searchTerm: params.term,
      };
    },
    processResults: function (response) {
      return {
        results: response,
      };
    },
    cache: true,
  };

  var select2Common = {
    width: "100%",
    minimumInputLength: 0,
    allowClear: true,
  };

  $("#pos_select").select2(
    $.extend({}, select2Common, {
      placeholder: select_patient,
      dropdownParent: select2DropdownParent($("#pos_select")),
      ajax: patientAjax,
    })
  );
  $("#pos_select1").select2(
    $.extend({}, select2Common, {
      placeholder: select_patient,
      dropdownParent: select2DropdownParent($("#pos_select1")),
      ajax: patientAjax,
    })
  );

  $("#adoctors").select2(
    $.extend({}, select2Common, {
      placeholder: select_doctor,
      dropdownParent: select2DropdownParent($("#adoctors")),
      ajax: doctorAjax,
    })
  );
  $("#adoctors1").select2(
    $.extend({}, select2Common, {
      placeholder: select_doctor,
      dropdownParent: select2DropdownParent($("#adoctors1")),
      ajax: doctorAjax,
    })
  );

  $("#myModal, #myModal2").on("shown.bs.modal", function () {
    var $modal = $(this);
    $modal.find(".select2-container").css("width", "100%");
    $modal.find(".select2-container .select2-selection--single").css("width", "100%");
  });
});










$(document).ready(function () {
  $("#visit_description").change(function () {
    var id = $(this).val();
    $("#visit_charges").val(" ");
    $.ajax({
      url: "doctor/getDoctorVisitCharges?id=" + id,
      method: "GET",
      dataType: "json",
      success: function (response) {
        $("#visit_charges").val(response.response.visit_charges).end();
        var discount = $("#discount").val();
        $("#grand_total")
          .val(parseFloat(response.response.visit_charges - discount))
          .end();
      },
    });
  });
  $("#discount").keyup(function () {
    var discount = $(this).val();
    var price = $("#visit_charges").val();
    $("#grand_total")
      .val(parseFloat(price - discount))
      .end();
  });
});


$(document).ready(function () {
  $("#visit_description1").change(function () {
    var id = $(this).val();
    $("#visit_charges1").val(" ");
    $.ajax({
      url: "doctor/getDoctorVisitCharges?id=" + id,
      method: "GET",
      dataType: "json",
      success: function(response) {
        $("#visit_charges1").val(response.response.visit_charges).end();
        var discount = $("#discount1").val();
        $("#grand_total1")
          .val(parseFloat(response.response.visit_charges - discount))
          .end();
      },
    });
  });
  $("#discount1").keyup(function () {
    var discount = $(this).val();
    var price = $("#visit_charges1").val();
    $("#grand_total1")
      .val(parseFloat(price - discount))
      .end();
  });
});






function cardValidation() {
  "use strict";
  var valid = true;
  var cardNumber = $("#card").val();
  var expire = $("#expire").val();
  var cvc = $("#cvv").val();

  $("#error-message").html("").hide();

  if (cardNumber.trim() == "") {
    valid = false;
  }

  if (expire.trim() == "") {
    valid = false;
  }
  if (cvc.trim() == "") {
    valid = false;
  }

  if (valid == false) {
    $("#error-message").html("All Fields are required").show();
  }

  return valid;
}
//set your publishable key
Stripe.setPublishableKey(publish);

//callback to handle the response from stripe
function stripeResponseHandler(status, response) {
  "use strict";
  if (response.error) {
    $("#submit-btn").show();
    $("#loader").css("display", "none");

    $("#error-message").html(response.error.message).show();
    $("#submit-btn").attr("disabled", false);
    $("#error-message").html(response.error.message).show();
  } else {
    var token = response["id"];
    if (token != null) {
      $("#token").val(token);
      $("#addAppointmentForm").append(
        "<input type='hidden' name='token' value='" + token + "' />"
      );
      $("#addAppointmentForm").submit();
    } else {
      alert("Please Check Your Card details");
      $("#submit-btn").attr("disabled", false);
    }
  }
}

function stripePay(e) {
  "use strict";
  e.preventDefault();
  var valid = cardValidation();

  if (valid == true) {
    $("#submit-btn").attr("disabled", true);
    $("#loader").css("display", "inline-block");
    var expire = $("#expire").val();
    var arr = expire.split("/");
    Stripe.createToken(
      {
        number: $("#card").val(),
        cvc: $("#cvv").val(),
        exp_month: arr[0],
        exp_year: arr[1],
      },
      stripeResponseHandler
    );

    return false;
  }
}
function cardValidation1() {
  var valid = true;
  var cardNumber = $("#card1").val();
  var expire = $("#expire1").val();
  var cvc = $("#cvv1").val();

  $("#error-message").html("").hide();

  if (cardNumber.trim() == "") {
    valid = false;
  }

  if (expire.trim() == "") {
    valid = false;
  }
  if (cvc.trim() == "") {
    valid = false;
  }

  if (valid == false) {
    $("#error-message").html("All Fields are required").show();
  }

  return valid;
}
//set your publishable key
Stripe.setPublishableKey(publish);

//callback to handle the response from stripe
function stripeResponseHandler1(status, response) {
  if (response.error) {
    //enable the submit button
    $("#submit-btn1").show();
    $("#loader").css("display", "none");
    //display the errors on the form
    $("#error-message").html(response.error.message).show();
  } else {
    //get token id
    var token = response["id"];
    //insert the token into the form
    $("#token").val(token);
    $("#editAppointmentForm").append(
      "<input type='hidden' name='token' value='" + token + "' />"
    );
    //submit form to the server
    $("#editAppointmentForm").submit();
  }
}

function stripePay1(e) {
  e.preventDefault();
  var valid = cardValidation1();

  if (valid == true) {
    $("#submit-btn1").attr("disabled", true);
    $("#loader").css("display", "inline-block");
    var expire = $("#expire1").val();
    var arr = expire.split("/");
    Stripe.createToken(
      {
        number: $("#card1").val(),
        cvc: $("#cvv1").val(),
        exp_month: arr[0],
        exp_year: arr[1],
      },
      stripeResponseHandler1
    );

    //submit from callback
    return false;
  }
}

if (payment_gateway == "2Checkout") {
  var successCallback = function (data) {
    "use strict";
    var myForm = document.getElementById("addAppointmentForm");

    $("#addAppointmentForm").append(
      "<input type='hidden' name='token' value='" +
      data.response.token.token +
      "' />"
    );

    myForm.submit();
  };
  // Called when token creation fails.
  var errorCallback = function (data) {
    "use strict";
    if (data.errorCode === 200) {
      tokenRequest();
    } else {
      alert(data.errorMsg);
    }
  };
  var tokenRequest = function () {
    "use strict";
    var expire = $("#expire").val();
    var expiresep = expire.split("/");
    var dateformat = moment(expiresep[1], "YY");
    var year = dateformat.format("YYYY");
    var args = {
      sellerId: merchant,
      publishableKey: publishable,
      ccNo: $("#card").val(),
      cvv: $("#cvv").val(),
      expMonth: expiresep[0],
      expYear: year,
    };
    console.log(
      $("#card").val() + "-" + $("#cvv").val() + expiresep[0] + year + merchant
    );

    TCO.requestToken(successCallback, errorCallback, args);
  };

  function twoCheckoutPay(e) {
    "use strict";
    e.preventDefault();

    TCO.loadPubKey("sandbox", function () {
      // for sandbox environment
      publishableKey = publishable; //your public key
      tokenRequest();
    });

    return false;
  }

  var successCallback1 = function (data) {
    "use strict";
    var myForm = document.getElementById("editAppointmentForm");

    $("#editAppointmentForm").append(
      "<input type='hidden' name='token' value='" +
      data.response.token.token +
      "' />"
    );

    myForm.submit();
  };
  // Called when token creation fails.
  var errorCallback1 = function (data) {
    "use strict";
    if (data.errorCode === 200) {
      tokenRequest1();
    } else {
      alert(data.errorMsg);
    }
  };
  var tokenRequest1 = function () {
    "use strict";
    var expire = $("#expire1").val();
    var expiresep = expire.split("/");
    var dateformat = moment(expiresep[1], "YY");
    var year = dateformat.format("YYYY");
    var args = {
      sellerId: merchant,
      publishableKey: publishable,
      ccNo: $("#card1").val(),
      cvv: $("#cvv1").val(),
      expMonth: expiresep[0],
      expYear: year,
    };
    console.log(
      $("#card1").val() +
      "-" +
      $("#cvv1").val() +
      expiresep[0] +
      year +
      merchant
    );

    TCO.requestToken(successCallback, errorCallback, args);
  };

  function twoCheckoutPay1(e) {
    "use strict";
    e.preventDefault();

    TCO.loadPubKey("sandbox", function () {
      // for sandbox environment
      publishableKey = publishable; //your public key
      tokenRequest1();
    });

    return false;
  }
}
