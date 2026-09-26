//Necessário criar uma simulação de cenário para passar pelo DOM da página, pois o Jest não possui acesso ao DOM do navegador.
global.document = {
    getElementById: (id) => ({ value: '', style: {}, textContent: '' }),
    addEventListener: () => {}
};


//Importar a função validarCpf do javascript/cadastro.js
const { validarCpf } = require('../javascript/cadastro.js');


//Cenário do teste
describe('CENÁRIO: Validação de CPF', () => {

    test.only('CT18: Verificação de CPF inválido (9 dígitos somente)', () => {
        const resultado = validarCpf('111111111');
        expect(resultado).toBe(false);
    });

});