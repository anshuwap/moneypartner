class Validation {

    required(v) {
        if (v.val == '') {
            $('#'+v.id).html(`<span class="validation-msg" id="required">${v.name} field is Required.</span>`);
            return false;
        } else {
            $('#'+v.id+'> span#required').remove();
            return true;
        }

    }

    max(v) {
        if (v.val.length > v.max) {
            $('#' + v.id).html(`<span class="validation-msg" id="max">${v.name} field should not be maximum ${v.max} Characters.</span>`);
            return false;
          } else {
              $('#'+v.id+'> span#max').remove();
              return true;
        }
    }

    min(v) {
        if (v.val.length < v.min) {
            $('#' + v.id).html(`<span class="validation-msg" id="min">${v.name} field should not be minimum ${v.min} Characters.</span>`);
            return false;
          } else {
              $('#'+v.id+'> span#min').remove();
              return true;
        }
    }

    phone(v) {
        var reg = /^(?:[1-9]\d*|\d)$/;
        if (!reg.test(v.val) && v.val.length > 1) {
            $('#' + v.id).html(`<span class="validation-msg" id="phoneNo">Please enter valid ${v.name}.</span>`);
            return false;
        } else {
          $('#'+v.id+'> span#phoneNo').remove();
          return true;
        }
    }

    exact(v) {
        if (v.val.length != v.digit && v.val.length > 1) {
            $('#' + v.id).html(`<span class="validation-msg" id="exact">${v.name} field must be ${v.digit} Characters.</span>`);
            return false;
        } else {
          $('#'+v.id+'> span#exact').remove();
          return true;
        }
    }

    email(v) {
        var email_regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if (!email_regex.test(v.val) && v.val.length > 1) {
            $('#' + v.id).html(`<span class="validation-msg" id="emailr">Please enter valid Email.</span>`);
            return false;
        } else {
          $('#'+v.id+'> span#emailr').remove();
          return true;
        }
    }

    //seprate function
    file(val, id) {
        if (v.val.length) {
            $('#' + v.id).html(`<span class="validation-msg" id="exact">Please Select a CSV File.</span>`);
        } else {
            $('#' + v.id).html('');
        }
    }
  }
