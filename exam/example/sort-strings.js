function stringsSort(arr) {
    for (let j = arr.length - 1; j > 0; j--) {
      for (let i = 0; i < j; i++) {
        if (arr[i].length > arr[i + 1].length) {
          let temp = arr[i];
          arr[i] = arr[i + 1];
          arr[i + 1] = temp;
        }
      }
    }
    return arr; // Возврат значения из функции
  }
arr1 = ['cherry','ap','ban','potate','dspoidspjpojpop'];
console.log(stringsSort(arr1)); // console.log // - это метод вывода в консоль