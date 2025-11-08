function sort(arr){
    let new_arr = [];
    for(i=0; i <= arr.length-1; i++){
        new_arr[arr[i].length] = arr[i];
    }
    return new_arr = new_arr.filter(Boolean);
}
let arr1 = ["banana", "apple", "kiwi", 'sajioasjiosaiojasjoisaoiasioj'];
console.log(sort(arr1));