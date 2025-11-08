
// Задача 6: Реализуйте сортировку выбором для массива строк. Например,
// для входного массива ["banana", "apple", "kiwi"] результат должен быть ["apple", "banana", "kiwi"].
// (отсортировали по алфавиту)
function sorta(arr){
    let alfabet = ['a', 'b', 'c', 'k'];
    let new_arr = [];
    for(i = 0; i <= arr.length -1; i++){
        let symbol = arr[i][0];
        for(j = 0; j <= alfabet.length -1; j++){
            if(symbol == alfabet[j]){
                new_arr[j] = arr[i];
            }
        }
    }
    return new_arr = new_arr.filter(Boolean);
}
let arr1 = ["banana", "apple", "kiwi"];
console.log(sorta(arr1));