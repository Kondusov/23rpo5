// Наивная рекурсивная реализация - НЕ рекомендуется для больших n
function fibonacciRecursive(n) {
    console.log(n)
    if (n <= 1) {
        return n;
    }
    return fibonacciRecursive(n - 1) + fibonacciRecursive(n - 2);
}
console.log(fibonacciRecursive(5)); //0112358