
// Реализация может отличаться в зависимости от структуры графа (например, матрица смежности или список смежности)
// и структуры очереди с приоритетом.

function dijkstra(graph, startNode) {
    // distances: объект для хранения кратчайшего расстояния от startNode до каждой другой вершины. Изначально все бесконечно, кроме startNode (0).
    const distances = {};
    // visited: массив для отслеживания посещенных вершин.
    const visited = new Set();
    // previous: объект для хранения предыдущей вершины на кратчайшем пути.
    const previous = {};
    // nodes: все вершины в графе.
    const nodes = Object.keys(graph);

    // Инициализация расстояний
    nodes.forEach(node => {
        distances[node] = Infinity;
        previous[node] = null;
    });
    distances[startNode] = 0;

    // Имитация очереди с приоритетом: в реальной реализации использовалась бы специализированная структура.
    // Для простоты будем использовать массив и находить минимум вручную.

    while (nodes.length) {
        // 1. Найти непосещенную вершину с наименьшим расстоянием
        let closestNode = null;
        for (const node of nodes) {
            if (closestNode === null || distances[node] < distances[closestNode]) {
                closestNode = node;
            }
        }

        // Если самая близкая вершина — бесконечность, значит, оставшиеся вершины недостижимы.
        if (distances[closestNode] === Infinity) break;

        // 2. Удалить самую близкую вершину из списка непосещенных и пометить как посещенную
        nodes.splice(nodes.indexOf(closestNode), 1);
        visited.add(closestNode);

        // 3. Обновить расстояния до соседей
        const neighbors = graph[closestNode];
        for (const neighbor in neighbors) {
            if (!visited.has(neighbor)) {
                const distanceToNeighbor = neighbors[neighbor];
                const totalDistance = distances[closestNode] + distanceToNeighbor;

                if (totalDistance < distances[neighbor]) {
                    distances[neighbor] = totalDistance;
                    previous[neighbor] = closestNode;
                }
            }
        }
    }

    return { distances, previous };
}

// Пример использования
const graph = {
    'A': { 'B': 4, 'C': 2 },
    'B': { 'A': 4, 'C': 5, 'D': 10 },
    'C': { 'A': 2, 'B': 5, 'D': 3 },
    'D': { 'B': 10, 'C': 3 }
};

const start = 'A';
const result = dijkstra(graph, start);

console.log("Кратчайшие расстояния от", start, ":", result.distances);
// Вывод: Кратчайшие расстояния от A : { A: 0, B: 4, C: 2, D: 5 }

console.log("Предыдущие вершины на кратчайших путях:", result.previous);
// Вывод: Предыдущие вершины на кратчайших путях: { A: null, B: 'A', C: 'A', D: 'C' }

function reconstructPath(previous, startNode, endNode) {
    const path = [];
    let current = endNode;
    while (current !== null) {
        path.unshift(current);
        current = previous[current];
    }
    return path.join(' -> ');
}

console.log("Кратчайший путь от", start, "до D:", reconstructPath(result.previous, start, 'D'));
// Вывод: Кратчайший путь от A до D: A -> C -> D