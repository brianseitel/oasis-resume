
// Generate some data.
function f1 (x) {
    return (2 * x) * x;
}

function f2 (x) {
    return 0.5 * Math.cos(x - 0.5) + 0.1;
}

var xmin = -1.0,
    xmax = 100,
    N = 100,
    data = [
        { x: 0, y: 0 },
        { x: 3, y: 4 },
        { x: 6, y: 20 },
        { x: 11, y: 8 },
        { x: 18, y: 18 },
        { x: 21, y: 25 },
        { x: 28, y: 30 },
        { x: 30, y: 50 },
        { x: 35, y: 80 },
    ];
var plot = xkcdplot({
    width: 1000, 
    height: 250,
    ylabel: 'Awesomeness Level',
    xlabel: 'Time',
    title: 'Awesomeness over Time'
});

plot("#chart");
plot.plot(data);
plot.xlim([-1.5, 7.5]).draw();