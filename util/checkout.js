const methodCr = document.getElementById('cr');
const methodGi = document.getElementById('gi');
const same = document.getElementById('same');
const billing = document.getElementById('billing').childNodes[3];
const shipping = document.getElementById('shipping').childNodes[7];
const cardInfo = document.getElementById('cardInfo').childNodes[7];
const form = document.getElementById('form');
const submit = document.getElementById('submit');
const email = document.getElementById('email');

const shippingArr = [shipping.childNodes['3'], shipping.childNodes['8'], shipping.childNodes['13'], shipping.childNodes['18'], shipping.childNodes['20'], email];
const billingArr = [billing.childNodes['3'], billing.childNodes['8'], billing.childNodes['13'], billing.childNodes['18'], billing.childNodes['20']];
const cardArr = [cardInfo.childNodes['3'], cardInfo.childNodes['8'], cardInfo.childNodes['10'], cardInfo.childNodes['15']];
const states = ['AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'FL', 'GA', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VA', 'WA', 'WV', 'WI', 'WY'];

const inputArr = [shippingArr, billingArr, cardArr];



methodGi.addEventListener('change', changeMethod);
methodCr.addEventListener('change', changeMethod);

function changeMethod(element) {
    if(element.target == methodCr) {
        cardArr[3].removeAttribute('disabled');
    } else if(element.target == methodGi){
        cardArr[3].setAttribute('disabled', 'disabled');
        cardArr[3].classList.add('ignore');
        cardArr[3].value = '';
    }
}

same.addEventListener('change', sameAddressChange);

function sameAddressChange(element) {
    
    
    if (element.srcElement.checked) {
        for (var i = 0; i < shippingArr.length; i++) {
            shippingArr[i].setAttribute('value', billingArr[i].value);
            shippingArr[i].setAttribute('disabled', 'disabled');
            shippingArr[i].classList.remove('invalid');
        }
    } else {
        for (var i = 0; i < shippingArr.length; i++) {
            shippingArr[i].removeAttribute('value');
            shippingArr[i].removeAttribute('disabled');
        }
    }
}

inputArr.forEach(array => {
    array.forEach(element => {
        element.addEventListener('focusout', validateInput);
    });
});

function validateInput(element) {
    const eleName = element.target.name;
    const eleValue = element.target.value;

    var regExp = null;
    switch (eleName) {
        case 'cardNum':
            regExp = /^(\d{4}-){3}\d{4}$/g;
            break;
        case 'expDate':
            regExp = /^\d{2}\/\d{2}$/g;
            break;
        case 'secNum':
            regExp = /^\d{3}$/g;
            break;
        case 'name':
            regExp = /^([a-zA-Z]{2,} ){1,2}[a-zA-Z]{2,}/g;
            break;
        case 'bill_line_1':
            regExp = /^\d+ [a-zA-Z]+ \d* ?[a-zA-Z]+/g;
            break;
        case 'bill_line_2':
            regExp = /^[a-zA-Z#\d]{0,10}/g;
            break;
        case 'bill_city':
            regExp = /^[a-zA-Z ]{1,50}/g;
            break;
        case 'bill_zip':
            regExp = /^\d{5}(-\d{4})?/;
            break;
        case 'ship_line_1':
            regExp = /^\d+ [a-zA-Z]+ \d* ?[a-zA-Z]+/g;
            break;
        case 'ship_line_2':
            regExp = /^[a-zA-Z#\d]{0,10}/g;
            break;
        case 'ship_city':
            regExp = /^[a-zA-Z ]{1,50}/g;
            break;
        case 'ship_zip':
            regExp = /^\d{5}(-\d{4})?/;
            break;
        case 'email':
            regExp = /\b[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}\b/;
            break;
        default:
            break;
    }

    if (regExp) {
        if (!eleValue.search(regExp)) {
            element.target.classList.remove('invalid');
        } else {
            element.target.classList.add('invalid');
        }
    } else if (eleName == 'bill_state' || eleName == 'ship_state'){
        if (states.includes(eleValue)) {
            element.target.classList.remove('invalid');
        } else {
            element.target.classList.add('invalid');
        }
    }
}

form.addEventListener('change', validateForm) 

function validateForm() {
    var invalid = false;

    inputArr.forEach(array => {
        array.forEach(element => {
            if (element.name == 'bill_line_2' || element.name == 'ship_line_2') {
                if (element.classList.contains('invalid')) {
                    invalid = true;
                }
            }
            else if ((element.classList.contains('invalid') || element.value == '' ) && !element.classList.contains('ignore')) {
                invalid = true;
            }
        });
    });

    if (!invalid) {
        submit.removeAttribute('disabled');
    } else {
        submit.setAttribute('disabled', 'disabled');
    }
}

cardArr[0].addEventListener("input", autoDashes);

function autoDashes(element) {
    const eleValue = element.target.value;
    if (eleValue.length % 5 == 0 && element.inputType != "deleteContentBackward" && element.data != "-") {
        var chars = eleValue.split('');
        var input = chars.pop();
        var output = '';

        chars.forEach(char => {
            output += char;
        });
        output += '-';
        output += input;

        element.target.value = output;
    }
}

cardArr[1].addEventListener("input", autoSlashes);

function autoSlashes(element) {
    const eleValue = element.target.value;

    if (eleValue.length % 3 == 0 && element.inputType != "deleteContentBackward" ** element.data != "/") {
        var chars = eleValue.split('');
        var input = chars.pop();
        var output = '';

        chars.forEach(char => {
            output += char;
        });
        output += '/';
        output += input;

        element.target.value = output;
    }
}