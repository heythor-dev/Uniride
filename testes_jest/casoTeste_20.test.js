// Necessário mock para simular DOM e inpus
let mockInputs = {};
let mockChecked = {}; // Necessário para o checkbox do motorista

global.document = {
    getElementById: (id) => ({
        get value() { return mockInputs[id] || ''; },
        get checked() { return mockChecked[id] || false; }, //define a checkbox do motorista pra false
        style: {},
        textContent: ''
    }),
    addEventListener: () => {}
};

// mock do SweetAlert para que o Jest "espiae" se a função foi chamada
global.Swal = {
    fire: jest.fn()
};
global.fetch = jest.fn(() => 
    Promise.resolve({ 
        json: () => Promise.resolve({ status: "livre" }) 
    })
); // Evita que a função tente fazer chamadas reais ao PHP
global.FormData = class FormData { append() {} }; // Simula uma construção do formulário

// Função novo importada
const { novo } = require('../javascript/cadastro.js');

//Limpar
beforeEach(() => {
    mockInputs = {};
    mockChecked = {};
    jest.clearAllMocks();
});

// Teste
describe('CENÁRIO: Validação de E-mail (via fluxo principal)', () => {

    test('CT20: Verificação de email em formato inválido', async () => {
        // email errado
        mockInputs = { nome: "Teste", senha: "123", email: "aluno@", nascimento: "2000-01-01" };
        mockChecked = { motorista: false };

        await novo();

        // Não deve chamar fetch pro php e sim aviso
        expect(Swal.fire).toHaveBeenCalledWith(expect.objectContaining({ 
            text: "Use seu email institucional!" 
        }));
    });

});