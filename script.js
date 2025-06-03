document.addEventListener('DOMContentLoaded', function () {
    const mainBtn = document.getElementById('l20cta-main-btn');
    const btnContainer = document.querySelector('.l20cta-buttons');

    let buttons = [];

    // Buttongegevens laden uit gelokaliseerde data
    const data = l20ctaData;
    const days = ['zondag','maandag','dinsdag','woensdag','donderdag','vrijdag','zaterdag'];
    const now = new Date();
    const today = days[now.getDay()];
    const currentTime = now.getHours() * 60 + now.getMinutes();

    const opening = data.openings[today];
    let isPhoneOpen = false;

    if (opening && opening.includes('-')) {
        const [start, end] = opening.split('-');
        const [startH, startM] = start.split(':').map(Number);
        const [endH, endM] = end.split(':').map(Number);
        const startMin = startH * 60 + startM;
        const endMin = endH * 60 + endM;

        isPhoneOpen = currentTime >= startMin && currentTime <= endMin;
    }

    const rawButtons = [
        { type: 'phone', url: data.phone_url, icon: 'fa-phone', iconColor: data.phone_icon_color, bgColor: data.phone_bg_color, order: parseInt(data.phone_order), show: isPhoneOpen },
        { type: 'email', url: data.email_url, icon: 'fa-envelope', iconColor: data.email_icon_color, bgColor: data.email_bg_color, order: parseInt(data.email_order), show: !!data.email_url },
        { type: 'whatsapp', url: data.whatsapp_url, icon: 'fa-brands fa-whatsapp', iconColor: data.whatsapp_icon_color, bgColor: data.whatsapp_bg_color, order: parseInt(data.whatsapp_order), show: !!data.whatsapp_url },
        { type: 'form', url: data.form_url, icon: 'fa-pen-to-square', iconColor: data.form_icon_color, bgColor: data.form_bg_color, order: parseInt(data.form_order), show: !!data.form_url, targetBlank: data.form_target_blank },
    ];

    // Filter en sorteer
    buttons = rawButtons.filter(btn => btn.url && btn.show).sort((a, b) => a.order - b.order);

    buttons.forEach(btn => {
        const button = document.createElement('a');
        button.href = btn.url;
        button.className = 'l20cta-btn';
        button.style.backgroundColor = btn.bgColor;
        button.style.color = btn.iconColor;
        button.innerHTML = `<i class="fa ${btn.icon}"></i>`;

        if (btn.type === 'form') {
            button.target = btn.targetBlank ? '_blank' : '_self';
        } else {
            button.target = '_blank';
        }

        // Voeg click event toe voor dataLayer tracking
        button.addEventListener('click', () => {
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({
                event: 'cta_button_click',
                ctaType: btn.type,
                ctaUrl: btn.url
            });
        });

        btnContainer.appendChild(button);
    });

    // Hoofdknop kleuren instellen
    mainBtn.style.backgroundColor = data.main_bg_color;
    mainBtn.querySelector('i').style.color = data.main_icon_color;

    // Initiale status ophalen uit localStorage
    const menuClosed = localStorage.getItem('l20cta-closed') === 'true';
    if (!menuClosed) {
        btnContainer.classList.add('l20cta-visible');
        mainBtn.classList.add('l20cta-open');
    }

    // Toggle zichtbaar bij klik op hoofdknop
    mainBtn.addEventListener('click', function (event) {
        event.stopPropagation();
        const isOpen = btnContainer.classList.toggle('l20cta-visible');
        mainBtn.classList.toggle('l20cta-open');

        localStorage.setItem('l20cta-closed', isOpen ? 'false' : 'true');
    });

    // Klik buiten het menu sluit het menu niet meer
});
