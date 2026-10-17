const BG_CONFIG = {
    colors: {
        primary: '#ffffff',
        particleAlpha: 0.92,
        lineAlphaMin: 0.08,
        fallbackParticle: '#ffffff'
    },
    motion: {
        fps: 30,
        fallbackParticleCount: 40,
        fallbackMouseRadius: 130,
        fallbackConnectDistance: 140,
        fallbackVelocity: 0.8,
        webglDprLimit: 1.5,
        desktopParticles: 180,
        mobileParticles: 120,
        maxSpeed: 0.9,
        minParticleSize: 2.6,
        maxParticleSize: 5.4,
        connectDistance: 150,
        mouseRadius: 170,
        mouseInfluence: 2.0,
        connectionWidth: 1,
        lineAlphaMin: 0.08,
        fallbackGlow: 8
    }
};

function hexToRgb(hex) {
    const normalized = hex.replace('#', '');
    const expanded = normalized.length === 3
        ? normalized.split('').map((char) => char + char).join('')
        : normalized;

    const parsed = parseInt(expanded, 16);
    if (!Number.isFinite(parsed) || expanded.length !== 6) {
        throw new Error(`Invalid hex color: ${hex}`);
    }

    return [
        (parsed >> 16) & 255,
        (parsed >> 8) & 255,
        parsed & 255
    ];
}

const primaryRgb = hexToRgb(BG_CONFIG.colors.primary);
const particleColor = [
    primaryRgb[0] / 255,
    primaryRgb[1] / 255,
    primaryRgb[2] / 255,
    BG_CONFIG.colors.particleAlpha
];

const canvas = document.getElementById('bgCanvas') || document.createElement('canvas');
canvas.id = 'bgCanvas';
canvas.setAttribute('aria-hidden', 'true');

if (!canvas.parentNode) {
    document.body.prepend(canvas);
}

Object.assign(canvas.style, {
    position: 'fixed',
    inset: '0',
    width: '100%',
    height: '100%',
    zIndex: '-1',
    pointerEvents: 'none',
    display: 'block'
});

document.body.style.position = 'relative';

const gl = canvas.getContext('webgl', {
    alpha: true,
    antialias: false,
    depth: false,
    stencil: false,
    preserveDrawingBuffer: false,
    powerPreference: 'high-performance'
});

const lineCanvas = document.createElement('canvas');
lineCanvas.id = 'bgCanvasLines';
lineCanvas.setAttribute('aria-hidden', 'true');
lineCanvas.style.position = 'fixed';
lineCanvas.style.inset = '0';
lineCanvas.style.width = '100%';
lineCanvas.style.height = '100%';
lineCanvas.style.zIndex = '-1';
lineCanvas.style.pointerEvents = 'none';
lineCanvas.style.display = 'block';
document.body.prepend(lineCanvas);

const lineCtx = lineCanvas.getContext('2d');

if (!gl) {
    const ctx = canvas.getContext('2d');
    if (!ctx) {
        throw new Error('Unable to initialize a rendering context for the background effect.');
    }

    const mouse = { x: null, y: null, radius: BG_CONFIG.motion.fallbackMouseRadius };
    const count = BG_CONFIG.motion.fallbackParticleCount;
    const particles = [];

    function resizeFallbackCanvas() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        lineCanvas.width = window.innerWidth;
        lineCanvas.height = window.innerHeight;
    }

    function initFallbackParticles() {
        particles.length = 0;
        for (let i = 0; i < count; i++) {
            particles.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                vx: (Math.random() - 0.5) * BG_CONFIG.motion.fallbackVelocity,
                vy: (Math.random() - 0.5) * BG_CONFIG.motion.fallbackVelocity,
                size: Math.random() * 2 + 1
            });
        }
    }

    window.addEventListener('mousemove', (event) => {
        mouse.x = event.x;
        mouse.y = event.y;
    });

    window.addEventListener('mouseout', () => {
        mouse.x = null;
        mouse.y = null;
    });

    window.addEventListener('resize', () => {
        resizeFallbackCanvas();
        initFallbackParticles();
    });

    resizeFallbackCanvas();
    initFallbackParticles();

    let lastFallbackFrame = 0;
    function animateFallback(now) {
        requestAnimationFrame(animateFallback);
        if (document.hidden || now - lastFallbackFrame < 1000 / BG_CONFIG.motion.fps) {
            return;
        }
        lastFallbackFrame = now;

        ctx.clearRect(0, 0, canvas.width, canvas.height);
        lineCtx.clearRect(0, 0, lineCanvas.width, lineCanvas.height);

        for (let i = 0; i < particles.length; i++) {
            const a = particles[i];
            if (a.x < 0 || a.x > canvas.width) a.vx *= -1;
            if (a.y < 0 || a.y > canvas.height) a.vy *= -1;

            if (mouse.x !== null && mouse.y !== null) {
                const dx = mouse.x - a.x;
                const dy = mouse.y - a.y;
                const distSq = dx * dx + dy * dy;
                if (distSq < mouse.radius * mouse.radius) {
                    a.x -= dx * 0.012;
                    a.y -= dy * 0.012;
                }
            }

            a.x += a.vx;
            a.y += a.vy;

            ctx.beginPath();
            ctx.fillStyle = BG_CONFIG.colors.fallbackParticle;
            ctx.shadowBlur = BG_CONFIG.motion.fallbackGlow;
            ctx.shadowColor = BG_CONFIG.colors.fallbackParticle;
            ctx.arc(a.x, a.y, a.size + 1, 0, Math.PI * 2);
            ctx.fill();
        }

        for (let i = 0; i < particles.length; i++) {
            for (let j = i + 1; j < particles.length; j++) {
                const a = particles[i];
                const b = particles[j];
                const dx = a.x - b.x;
                const dy = a.y - b.y;
                const dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < BG_CONFIG.motion.fallbackConnectDistance) {
                    const alpha = 1 - dist / BG_CONFIG.motion.fallbackConnectDistance;
                    lineCtx.beginPath();
                    lineCtx.strokeStyle = `rgba(${primaryRgb.join(', ')}, ${alpha})`;
                    lineCtx.lineWidth = BG_CONFIG.motion.connectionWidth;
                    lineCtx.moveTo(a.x, a.y);
                    lineCtx.lineTo(b.x, b.y);
                    lineCtx.stroke();
                }
            }
        }
    }

    requestAnimationFrame(animateFallback);
} else {
    const config = {
        dpr: Math.min(window.devicePixelRatio || 1, BG_CONFIG.motion.webglDprLimit),
        particleCount: window.innerWidth > 1024 ? BG_CONFIG.motion.desktopParticles : BG_CONFIG.motion.mobileParticles,
        maxSpeed: BG_CONFIG.motion.maxSpeed,
        minSize: BG_CONFIG.motion.minParticleSize,
        maxSize: BG_CONFIG.motion.maxParticleSize,
        connectDistance: BG_CONFIG.motion.connectDistance,
        mouseRadius: BG_CONFIG.motion.mouseRadius,
        mouseInfluence: BG_CONFIG.motion.mouseInfluence,
        lineWidth: BG_CONFIG.motion.connectionWidth,
        lineAlphaMin: BG_CONFIG.motion.lineAlphaMin
    };

    const mouse = { x: null, y: null };
    const positions = new Float32Array(config.particleCount * 2);
    const velocities = new Float32Array(config.particleCount * 2);
    const sizes = new Float32Array(config.particleCount);
    const vertexData = new Float32Array(config.particleCount * 3);

    let cssWidth = 0;
    let cssHeight = 0;
    let lastFrame = 0;

    const vertexShaderSource = `
        attribute vec2 a_position;
        attribute float a_size;
        uniform vec2 u_resolution;

        void main() {
            vec2 zeroToOne = a_position / u_resolution;
            vec2 zeroToTwo = zeroToOne * 2.0;
            vec2 clip = zeroToTwo - 1.0;
            gl_Position = vec4(clip * vec2(1.0, -1.0), 0.0, 1.0);
            gl_PointSize = a_size;
        }
    `;

    const fragmentShaderSource = `
        precision mediump float;
        uniform vec4 u_color;

        void main() {
            vec2 c = gl_PointCoord - vec2(0.5, 0.5);
            float d = length(c);
            if (d > 0.5) {
                discard;
            }

            float alpha = smoothstep(0.5, 0.0, d) * u_color.a;
            gl_FragColor = vec4(u_color.rgb, alpha);
        }
    `;

    function createShader(type, source) {
        const shader = gl.createShader(type);
        if (!shader) {
            throw new Error('Unable to allocate WebGL shader.');
        }
        gl.shaderSource(shader, source);
        gl.compileShader(shader);

        if (!gl.getShaderParameter(shader, gl.COMPILE_STATUS)) {
            const message = gl.getShaderInfoLog(shader) || 'Unknown shader compile error.';
            gl.deleteShader(shader);
            throw new Error(message);
        }
        return shader;
    }

    function createProgram(vertexShader, fragmentShader) {
        const program = gl.createProgram();
        if (!program) {
            throw new Error('Unable to allocate WebGL program.');
        }
        gl.attachShader(program, vertexShader);
        gl.attachShader(program, fragmentShader);
        gl.linkProgram(program);

        if (!gl.getProgramParameter(program, gl.LINK_STATUS)) {
            const message = gl.getProgramInfoLog(program) || 'Unknown program link error.';
            gl.deleteProgram(program);
            throw new Error(message);
        }
        return program;
    }

    const vertexShader = createShader(gl.VERTEX_SHADER, vertexShaderSource);
    const fragmentShader = createShader(gl.FRAGMENT_SHADER, fragmentShaderSource);
    const program = createProgram(vertexShader, fragmentShader);

    const aPosition = gl.getAttribLocation(program, 'a_position');
    const aSize = gl.getAttribLocation(program, 'a_size');
    const uResolution = gl.getUniformLocation(program, 'u_resolution');
    const uColor = gl.getUniformLocation(program, 'u_color');

    const buffer = gl.createBuffer();
    if (!buffer) {
        throw new Error('Unable to allocate WebGL particle buffer.');
    }

    function resizeCanvas() {
        cssWidth = window.innerWidth;
        cssHeight = window.innerHeight;
        const width = Math.floor(cssWidth * config.dpr);
        const height = Math.floor(cssHeight * config.dpr);

        canvas.width = width;
        canvas.height = height;
        lineCanvas.width = cssWidth * config.dpr;
        lineCanvas.height = cssHeight * config.dpr;
        gl.viewport(0, 0, width, height);
    }

    function initParticles() {
        for (let i = 0; i < config.particleCount; i++) {
            const ix = i * 2;
            positions[ix] = Math.random() * cssWidth;
            positions[ix + 1] = Math.random() * cssHeight;
            velocities[ix] = (Math.random() - 0.5) * config.maxSpeed;
            velocities[ix + 1] = (Math.random() - 0.5) * config.maxSpeed;
            sizes[i] = config.minSize + Math.random() * (config.maxSize - config.minSize);
        }
    }

    function updateParticles() {
        for (let i = 0; i < config.particleCount; i++) {
            const ix = i * 2;
            let x = positions[ix];
            let y = positions[ix + 1];
            let vx = velocities[ix];
            let vy = velocities[ix + 1];

            if (x <= 0 || x >= cssWidth) vx *= -1;
            if (y <= 0 || y >= cssHeight) vy *= -1;

            if (mouse.x !== null && mouse.y !== null) {
                const dx = mouse.x - x;
                const dy = mouse.y - y;
                const distSq = dx * dx + dy * dy;
                const radiusSq = config.mouseRadius * config.mouseRadius;
                if (distSq < radiusSq && distSq > 0.001) {
                    const influence = (radiusSq - distSq) / radiusSq;
                    const invDist = 1 / Math.sqrt(distSq);
                    x -= dx * invDist * influence * config.mouseInfluence;
                    y -= dy * invDist * influence * config.mouseInfluence;
                }
            }

            x += vx;
            y += vy;

            positions[ix] = x;
            positions[ix + 1] = y;
            velocities[ix] = vx;
            velocities[ix + 1] = vy;
        }
    }

    function writeVertexBuffer() {
        for (let i = 0; i < config.particleCount; i++) {
            const sourceIx = i * 2;
            const targetIx = i * 3;
            vertexData[targetIx] = positions[sourceIx];
            vertexData[targetIx + 1] = positions[sourceIx + 1];
            vertexData[targetIx + 2] = sizes[i] * 4;
        }
    }

    function drawConnections() {
        lineCtx.clearRect(0, 0, lineCanvas.width, lineCanvas.height);

        for (let i = 0; i < config.particleCount; i++) {
            const ax = positions[i * 2];
            const ay = positions[i * 2 + 1];

            for (let j = i + 1; j < config.particleCount; j++) {
                const bx = positions[j * 2];
                const by = positions[j * 2 + 1];
                const dx = ax - bx;
                const dy = ay - by;
                const distSq = dx * dx + dy * dy;

                if (distSq < config.connectDistance * config.connectDistance) {
                    const alpha = 1 - Math.sqrt(distSq) / config.connectDistance;
                    lineCtx.beginPath();
                    lineCtx.strokeStyle = `rgba(${primaryRgb.join(', ')}, ${Math.max(alpha, config.lineAlphaMin)})`;
                    lineCtx.lineWidth = config.lineWidth;
                    lineCtx.moveTo(ax, ay);
                    lineCtx.lineTo(bx, by);
                    lineCtx.stroke();
                }
            }
        }
    }

    function drawParticles() {
        gl.clear(gl.COLOR_BUFFER_BIT);
        gl.useProgram(program);
        gl.uniform2f(uResolution, cssWidth, cssHeight);
        gl.uniform4f(uColor, particleColor[0], particleColor[1], particleColor[2], particleColor[3]);

        gl.bindBuffer(gl.ARRAY_BUFFER, buffer);
        gl.bufferData(gl.ARRAY_BUFFER, vertexData, gl.DYNAMIC_DRAW);

        const stride = 3 * Float32Array.BYTES_PER_ELEMENT;
        gl.enableVertexAttribArray(aPosition);
        gl.vertexAttribPointer(aPosition, 2, gl.FLOAT, false, stride, 0);

        gl.enableVertexAttribArray(aSize);
        gl.vertexAttribPointer(
            aSize,
            1,
            gl.FLOAT,
            false,
            stride,
            2 * Float32Array.BYTES_PER_ELEMENT
        );

        gl.drawArrays(gl.POINTS, 0, config.particleCount);
    }

    function animate(now) {
        requestAnimationFrame(animate);

        if (document.hidden || now - lastFrame < 1000 / BG_CONFIG.motion.fps) {
            return;
        }
        lastFrame = now;

        updateParticles();
        writeVertexBuffer();
        drawParticles();
        drawConnections();
    }

    window.addEventListener('mousemove', (event) => {
        mouse.x = event.x;
        mouse.y = event.y;
    });

    window.addEventListener('mouseout', () => {
        mouse.x = null;
        mouse.y = null;
    });

    window.addEventListener('resize', () => {
        resizeCanvas();
        initParticles();
    });

    resizeCanvas();
    initParticles();

    gl.clearColor(0, 0, 0, 0);
    gl.enable(gl.BLEND);
    gl.blendFunc(gl.SRC_ALPHA, gl.ONE);

    lineCtx.lineCap = 'round';
    lineCtx.lineJoin = 'round';

    requestAnimationFrame(animate);
}