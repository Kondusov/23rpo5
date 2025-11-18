function balance(str){
    let arr = str.split("");
    let counter = 0;
    for(symbol of arr){
        if(symbol == '(') counter++;
        if(symbol == ')') counter--;
    }
    if(counter==0) return "Строка сбалансированна";
    else return "Строка НЕ сбалансированна";
}

let text = 'dvds(  dfdfd)';
console.log(balance(text));