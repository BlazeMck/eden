console.log('This should print...');
const methodCr = document.getElementById('cr');
const methodGi = document.getElementById('gi');
const same = document.getElementById('same');
const billing = document.getElementById('billing').childNodes[3];
const shipping = document.getElementById('shipping').childNodes[7];
const cardInfo = document.getElementById('cardInfo').childNodes[7];
const form = document.getElementById('form');
const submit = document.getElementById('submit');
const email

const shippingArr = [shipping.childNodes['3'], shipping.childNodes['8'], shipping.childNodes['13'], shipping.childNodes['18'], shipping.childNodes['20']];
const billingArr = [billing.childNodes['3'], billing.childNodes['8'], billing.childNodes['13'], billing.childNodes['18'], billing.childNodes['20']];
const cardArr = [cardInfo.childNodes['3'], cardInfo.childNodes['8'], cardInfo.childNodes['10'], cardInfo.childNodes['15']];
const states = ['AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'FL', 'GA', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VA', 'WA', 'WV', 'WI', 'WY'];

const inputArr = [shippingArr, billingArr, cardArr];



methodGi.addEventListener('change', changeMethod);
methodCr.addEventListener('change', changeMethod);

function changeMethod(element) {
    if(element.target == methodCr) {
        cardArr[2].removeAttribute('disabled');
        cardArr[3].removeAttribute('disabled');
    } else if(element.target == methodGi){
        cardArr[2].setAttribute('disabled', 'disabled');
        cardArr[3].setAttribute('disabled', 'disabled');
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
            regExp = 
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
                    console.log(`invalid input: ${element.name}`);
                }
            }
            else if (element.classList.contains('invalid') || element.value == '' ) {
                invalid = true;
                console.log(`invalid input: ${element.name}`)
            }
        });
    });

    if (!invalid) {
        submit.removeAttribute('disabled');
    }
}
