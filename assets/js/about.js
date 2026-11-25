(() => {
    const terminal = document.querySelector('[data-terminal]');
    if (!terminal) {
        return;
    }

    const TYPE_DELAY = 45;
    const OUTPUT_DELAY = 250;
    const STEP_DELAY = 350;

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const steps = [...terminal.querySelectorAll('.terminal-step:not(.terminal-detail)')];
    const detail = terminal.querySelector('.terminal-detail');
    const detailCmd = detail.querySelector('.terminal-cmd');
    const detailOut = detail.querySelector('.terminal-out');
    const folders = [...terminal.querySelectorAll('.terminal-folder')];

    const wait = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

    let detailJob = 0;

    async function type(element, text, isCancelled = () => false) {
        element.textContent = '';
        for (const char of text) {
            if (isCancelled()) {
                return false;
            }
            element.textContent += char;
            await wait(TYPE_DELAY);
        }
        return true;
    }

    async function playIntro() {
        for (const step of steps) {
            const cmd = step.querySelector('.terminal-cmd');
            await type(cmd, cmd.dataset.text);
            await wait(OUTPUT_DELAY);
            step.classList.remove('is-pending');
            await wait(STEP_DELAY);
        }
    }

    async function showPassion(folder) {
        folders.forEach((item) => item.classList.toggle('active', item === folder));

        const job = ++detailJob;
        const command = `cat passions/${folder.dataset.folder}/README.txt`;

        detail.hidden = false;
        detailOut.textContent = folder.dataset.text;

        if (reducedMotion) {
            detailCmd.textContent = command;
            return;
        }

        detail.classList.add('is-pending');
        if (await type(detailCmd, command, () => job !== detailJob)) {
            await wait(OUTPUT_DELAY);
            detail.classList.remove('is-pending');
        }
    }

    folders.forEach((folder) => {
        folder.addEventListener('click', () => showPassion(folder));
    });

    if (reducedMotion) {
        return;
    }

    steps.forEach((step) => {
        const cmd = step.querySelector('.terminal-cmd');
        cmd.dataset.text = cmd.textContent;
        cmd.textContent = '';
        step.classList.add('is-pending');
    });

    const observer = new IntersectionObserver((entries) => {
        if (entries.some((entry) => entry.isIntersecting)) {
            observer.disconnect();
            playIntro();
        }
    }, { threshold: 0.15 });

    observer.observe(terminal);
})();
