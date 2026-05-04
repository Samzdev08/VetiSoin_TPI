function setValue(input, value) {
    document.getElementById(input).value = value;
}

const pagination = document.getElementById('pagination');
const articles = document.querySelectorAll('.article-card');
const ARTICLES_PAR_PAGE = 10;
let pages = Math.ceil(articles.length / ARTICLES_PAR_PAGE);
let count = 0;

function Affichage(page) {

    articles.forEach((item, index) => {
        item.style.display = Math.floor(index / ARTICLES_PAR_PAGE) === page ? '' : 'none';
    });

    pagination.innerHTML = '';
    for (let i = 0; i < pages; i++) {
        const btn = document.createElement('button');
        btn.textContent = i + 1;
        btn.classList.add('btn', 'm-1');

       
        if (i === page) {
            btn.classList.add('btn-primary');
        } else {
            btn.classList.add('btn-outline-primary');
        }

        btn.addEventListener('click', () => {
            count = i;
            Affichage(count);
        });

        pagination.appendChild(btn);
    }
}

Affichage(count);

document.querySelectorAll('.article-card').forEach(item => {
    item.addEventListener('click', () => {
        const id = item.getAttribute('data-id');
        window.location.href = `/catalogue/${id}`;
    });
});