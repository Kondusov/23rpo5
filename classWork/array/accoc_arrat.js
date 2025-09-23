let arr = []
let price = {
    'cherry' : 350
}

price.cherry = 400
price['cherry'] = 500
price.apple = 100
price['banana'] = 150
console.log(price)
console.log(typeof(arr))
console.log(typeof(price))
console.log(Array.isArray(arr))