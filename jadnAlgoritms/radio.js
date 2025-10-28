/**
 * Решение задачи о покрытии штатов радиостанциями с использованием жадного алгоритма
 * 
 * Жадный алгоритм на каждом шаге выбирает станцию, которая покрывает наибольшее количество
 * еще не покрытых штатов, пока все штаты не будут покрыты.
 */

/**
 * Функция для нахождения минимального набора станций, покрывающих все штаты
 * @param {Set} statesNeeded - Множество штатов, которые нужно покрыть
 * @param {Object} stations - Объект, где ключи - названия станций, значения - Set покрываемых штатов
 * @returns {Set} - Множество выбранных станций
 */
function findMinStationsCoverage(statesNeeded, stations) {
    // Создаем копию множества нужных штатов, чтобы не изменять оригинал
    const uncoveredStates = new Set(statesNeeded);
    // Множество для хранения выбранных станций
    const selectedStations = new Set();
    
    // Пока есть непокрытые штаты
    while (uncoveredStates.size > 0) {
        let bestStation = null;        // Лучшая станция на текущем шаге
        let bestCoverage = new Set();  // Штаты, покрываемые лучшей станцией
        let maxCoveredCount = 0;       // Максимальное количество покрываемых штатов
        
        // Перебираем все станции
        for (const [station, stationStates] of Object.entries(stations)) {
            // Если станция уже выбрана, пропускаем ее
            if (selectedStations.has(station)) {
                continue;
            }
            
            // Находим пересечение штатов, покрываемых станцией, и непокрытых штатов
            const covered = new Set(
                [...stationStates].filter(state => uncoveredStates.has(state))
            );
            
            // Если эта станция покрывает больше непокрытых штатов, чем текущая лучшая
            if (covered.size > maxCoveredCount) {
                bestStation = station;
                bestCoverage = covered;
                maxCoveredCount = covered.size;
            }
        }
        
        // Если не нашли подходящую станцию (теоретически не должно случиться при корректных данных)
        if (bestStation === null) {
            throw new Error("Невозможно покрыть все штаты доступными станциями");
        }
        
        // Добавляем лучшую станцию в результат
        selectedStations.add(bestStation);
        
        // Удаляем покрытые штаты из множества непокрытых
        for (const state of bestCoverage) {
            uncoveredStates.delete(state);
        }
        
        console.log(`Выбрана станция: ${bestStation}`);
        console.log(`Покрытые штаты: ${[...bestCoverage].join(', ')}`);
        console.log(`Осталось покрыть: ${uncoveredStates.size} штатов\n`);
    }
    
    return selectedStations;
}

// Пример использования

// Создаем множество всех штатов, которые нужно покрыть
const allStates = new Set();
for (let i = 1; i <= 50; i++) {
    allStates.add(`Штат${i}`);
}

// Определяем радиостанции и покрываемые ими штаты
const stations = {
    "Станция1": new Set(["Штат1", "Штат2", "Штат3", "Штат4", "Штат5", "Штат6"]),
    "Станция2": new Set(["Штат3", "Штат4", "Штат5", "Штат7", "Штат8", "Штат9"]),
    "Станция3": new Set(["Штат6", "Штат7", "Штат8", "Штат10", "Штат11", "Штат12"]),
    "Станция4": new Set(["Штат9", "Штат10", "Штат11", "Штат13", "Штат14", "Штат15"]),
    "Станция5": new Set(["Штат12", "Штат13", "Штат14", "Штат16", "Штат17", "Штат18"]),
    "Станция6": new Set(["Штат15", "Штат16", "Штат17", "Штат19", "Штат20", "Штат21"]),
    "Станция7": new Set(["Штат18", "Штат19", "Штат20", "Штат22", "Штат23", "Штат24"]),
    "Станция8": new Set(["Штат21", "Штат22", "Штат23", "Штат25", "Штат26", "Штат27"]),
    "Станция9": new Set(["Штат24", "Штат25", "Штат26", "Штат28", "Штат29", "Штат30"]),
    "Станция10": new Set(["Штат27", "Штат28", "Штат29", "Штат31", "Штат32", "Штат33"]),
    "Станция11": new Set(["Штат30", "Штат31", "Штат32", "Штат34", "Штат35", "Штат36"]),
    "Станция12": new Set(["Штат33", "Штат34", "Штат35", "Штат37", "Штат38", "Штат39"]),
    "Станция13": new Set(["Штат36", "Штат37", "Штат38", "Штат40", "Штат41", "Штат42"]),
    "Станция14": new Set(["Штат39", "Штат40", "Штат41", "Штат43", "Штат44", "Штат45"]),
    "Станция15": new Set(["Штат42", "Штат43", "Штат44", "Штат46", "Штат47", "Штат48"]),
    "Станция16": new Set(["Штат45", "Штат46", "Штат47", "Штат49", "Штат50"]),
    "Станция17": new Set(["Штат1", "Штат10", "Штат20", "Штат30", "Штат40", "Штат50"]),
    "Станция18": new Set(["Штат5", "Штат15", "Штат25", "Штат35", "Штат45"]),
};

console.log("Поиск минимального набора радиостанций для покрытия 50 штатов...\n");

try {
    const result = findMinStationsCoverage(allStates, stations);
    
    console.log("=" .repeat(50));
    console.log("РЕЗУЛЬТАТ:");
    console.log(`Минимальное количество станций: ${result.size}`);
    console.log(`Выбранные станции: ${[...result].join(', ')}`);
    
    // Проверка покрытия
    const coveredStates = new Set();
    for (const station of result) {
        for (const state of stations[station]) {
            coveredStates.add(state);
        }
    }
    
    console.log(`\nПроверка: покрыто ${coveredStates.size} из 50 штатов`);
    console.log(`Все штаты покрыты: ${coveredStates.size === 50}`);
    
} catch (error) {
    console.error("Ошибка:", error.message);
}

/**
 * Альтернативная версия функции для работы с реальными названиями штатов
 */
function findMinStationsCoverageRealStates() {
    // Пример с реальными названиями штатов США
    const usStates = new Set([
        "AL", "AK", "AZ", "AR", "CA", "CO", "CT", "DE", "FL", "GA",
        "HI", "ID", "IL", "IN", "IA", "KS", "KY", "LA", "ME", "MD",
        "MA", "MI", "MN", "MS", "MO", "MT", "NE", "NV", "NH", "NJ",
        "NM", "NY", "NC", "ND", "OH", "OK", "OR", "PA", "RI", "SC",
        "SD", "TN", "TX", "UT", "VT", "VA", "WA", "WV", "WI", "WY"
    ]);
    
    const realStations = {
        "WestCoastRadio": new Set(["CA", "OR", "WA", "NV", "AZ"]),
        "MidwestNetwork": new Set(["IL", "IN", "OH", "MI", "WI", "MN", "IA"]),
        "SouthernVoice": new Set(["TX", "FL", "GA", "AL", "MS", "LA", "TN"]),
        "EastCoastFM": new Set(["NY", "NJ", "PA", "CT", "MA", "RI", "NH", "VT"]),
        "CentralBroadcast": new Set(["CO", "KS", "NE", "MO", "OK", "AR"]),
        "NorthernLights": new Set(["MT", "ND", "SD", "WY", "ID"]),
        "PacificWave": new Set(["HI", "AK", "WA", "OR"]),
        "NationalCoverage": new Set(["CA", "TX", "NY", "FL", "IL", "PA", "OH", "GA"])
    };
    
    console.log("\n" + "=" .repeat(50));
    console.log("Пример с реальными названиями штатов:");
    
    const realResult = findMinStationsCoverage(usStates, realStations);
    console.log(`Выбранные станции: ${[...realResult].join(', ')}`);
}

// Запуск примера с реальными штатами
findMinStationsCoverageRealStates();