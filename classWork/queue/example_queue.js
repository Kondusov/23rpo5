function addElemQueue(arr, val){
    if(true){
    arr.push(val);
    console.log(arr)}
}
function getElemQueue(arr){
    if(arr.length > 0){
    arr.shift();
    console.log(arr)}
}
arr1 = []
// addElemQueue(arr1, 9);
// getElemQueue(arr1);
// getElemQueue(arr1);
function getSetElemQueue(arr, val){
    if(val!= undefined){
        arr.push(val);
        console.log(arr)}
    else{
        if(arr.length > 0){
            arr.shift();
            console.log(arr)}
        }
    }
getSetElemQueue(arr1, 1)
getSetElemQueue(arr1, 2)
getSetElemQueue(arr1, 3)
getSetElemQueue(arr1)
getSetElemQueue(arr1)
getSetElemQueue(arr1)
getSetElemQueue(arr1)