function recursive(i){
    console.log(i);
    if(i<=0){
       return i; 
    }
    else{
        recursive(i-1);
    }
}
recursive(5);