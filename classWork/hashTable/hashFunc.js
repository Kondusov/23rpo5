function addElem(table, elem, value){
    if(table[elem]){
        console.log('Внимание попытка создать коллизию!');
    }else{
        table[elem] = value;
    }
}
table = {'user1': 5, 'user2': 3};
//addElem(table, 'admin' , 0);
addElem(table, 'user1' , 0);
console.log(table);
