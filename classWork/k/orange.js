// Пример данных: массив объектов {x, y, label}
// x, y — координаты точки, label — её класс (например, 'A' или 'B')
const data = [
    { x: 1, y: 2, label: 'A' },
    { x: 2, y: 3, label: 'A' },
    { x: 3, y: 1, label: 'B' },
    { x: 5, y: 4, label: 'B' },
    { x: 6, y: 5, label: 'B' }
  ];
  
  // Функция для вычисления евклидова расстояния между двумя точками
  function euclideanDistance(point1, point2) {
    const dx = point1.x - point2.x;
    const dy = point1.y - point2.y;
    return Math.sqrt(dx * dx + dy * dy);
  }
  
  // Функция KNN: находит класс для новой точки
  function knnClassify(newPoint, data, k) {
    // 1. Вычисляем расстояния от newPoint до всех точек в data
    const distances = data.map(point => ({
      point: point,
      distance: euclideanDistance(newPoint, point)
    }));
  
    // 2. Сортируем точки по расстоянию (от меньшего к большему)
    distances.sort((a, b) => a.distance - b.distance);
  
    // 3. Берём первые k точек (ближайших соседей)
    const kNearest = distances.slice(0, k);
  
    // 4. Считаем, сколько соседей каждого класса
    const classCounts = {};
    for (const neighbor of kNearest) {
      const label = neighbor.point.label;
      classCounts[label] = (classCounts[label] || 0) + 1;
    }
  
    // 5. Находим класс с максимальным количеством голосов
    let predictedClass = null;
    let maxCount = 0;
    for (const [label, count] of Object.entries(classCounts)) {
      if (count > maxCount) {
        maxCount = count;
        predictedClass = label;
      }
    }
  
    return predictedClass;
  }
  
  // Пример использования
  const newPoint = { x: 4, y: 3 }; // Новая точка, класс которой нужно определить
  const k = 3; // Количество ближайших соседей
  
  const predictedLabel = knnClassify(newPoint, data, k);
  
  console.log(`Точка (${newPoint.x}, ${newPoint.y}) относится к классу: ${predictedLabel}`);
  