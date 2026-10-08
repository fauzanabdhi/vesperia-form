import '../css/app.css';

const app = document.querySelector('#app');
const formUrl = app.dataset.formUrl;

const loadingMessage = document.querySelector('#loading-message');
const loadError = document.querySelector('#load-error');
const formContainer = document.querySelector('#form-container');
const formTitle = document.querySelector('#form-title');
const formElement = document.querySelector('#dynamic-form');
const sectionsElement = document.querySelector('#sections');
const submitButton = document.querySelector('#submit-button');
const successMessage = document.querySelector('#success-message');
const submissionErrors = document.querySelector('#submission-errors');

function createElement(tag, options = {}) {
    const element = document.createElement(tag);

    if (options.className) {
        element.className = options.className;
    }

    if (options.text) {
        element.textContent = options.text;
    }

    return element;
}

function renderTextField(field) {
    const input = field.type === 'long_text'
        ? document.createElement('textarea')
        : document.createElement('input');

    if (field.type !== 'long_text') {
        input.type = field.sub_type === 'date'
            ? 'date'
            : field.sub_type === 'amount'
                ? 'number'
                : 'text';

        if (field.sub_type === 'amount') {
            input.step = '0.01';
        }
    }

    input.name = `answers[${field.id}]`;
    input.dataset.answerInput = 'true';

    return input;
}

function renderOptionField(field) {
    const optionsContainer = createElement('div', {
        className: 'options-container',
    });

    for (const option of field.options) {
        const optionLabel = createElement('label', {
            className: 'option-label',
        });

        const input = document.createElement('input');

        input.type = field.type === 'radio_button' ? 'radio' : 'checkbox';
        input.name = field.type === 'radio_button'
            ? `answers[${field.id}]`
            : `answers[${field.id}][]`;
        input.value = option.id;
        input.dataset.answerInput = 'true';

        optionLabel.append(input, document.createTextNode(` ${option.label}`));
        optionsContainer.append(optionLabel);
    }

    return optionsContainer;
}

function renderField(field) {
    const container = createElement('div', {
        className: 'field',
    });

    container.dataset.fieldId = field.id;
    container.dataset.fieldType = field.type;

    const label = createElement('label', {
        className: 'field-label',
        text: field.label,
    });

    container.append(label);

    if (field.description) {
        container.append(createElement('p', {
            className: 'field-description',
            text: field.description,
        }));
    }

    if (field.type === 'radio_button' || field.type === 'checkbox') {
        container.append(renderOptionField(field));
    } else if (field.type === 'text' || field.type === 'long_text') {
        container.append(renderTextField(field));
    } else {
        container.append(createElement('p', {
            className: 'error-message',
            text: `Unsupported field type: ${field.type}`,
        }));
    }

    const error = createElement('p', {
        className: 'field-error',
    });

    error.dataset.fieldError = field.id;
    error.hidden = true;

    container.append(error);

    return container;
}

function renderForm(form) {
    formTitle.textContent = form.name;

    for (const section of form.sections) {
        const sectionElement = createElement('section', {
            className: 'form-section',
        });

        sectionElement.append(createElement('h2', {
            text: section.name,
        }));

        for (const field of section.fields) {
            sectionElement.append(renderField(field));
        }

        sectionsElement.append(sectionElement);
    }
}

function collectAnswers() {
    const answers = {};

    for (const fieldElement of document.querySelectorAll('[data-field-id]')) {
        const fieldId = fieldElement.dataset.fieldId;
        const fieldType = fieldElement.dataset.fieldType;

        if (fieldType === 'radio_button') {
            const selected = fieldElement.querySelector('input:checked');

            if (selected) {
                answers[fieldId] = selected.value;
            }

            continue;
        }

        if (fieldType === 'checkbox') {
            const selected = [...fieldElement.querySelectorAll('input:checked')]
                .map((input) => input.value);

            if (selected.length > 0) {
                answers[fieldId] = selected;
            }

            continue;
        }

        const input = fieldElement.querySelector('[data-answer-input="true"]');

        if (input && input.value.trim() !== '') {
            answers[fieldId] = input.value;
        }
    }

    return answers;
}

function clearErrors() {
    submissionErrors.hidden = true;
    submissionErrors.textContent = '';

    for (const errorElement of document.querySelectorAll('[data-field-error]')) {
        errorElement.hidden = true;
        errorElement.textContent = '';
    }
}

function showValidationErrors(errors) {
    for (const [key, messages] of Object.entries(errors)) {
        const fieldId = key.replace('answers.', '');
        const errorElement = document.querySelector(
            `[data-field-error="${fieldId}"]`,
        );

        if (errorElement) {
            errorElement.textContent = messages.join(' ');
            errorElement.hidden = false;
        }
    }
}

async function loadForm() {
    try {
        const response = await fetch(formUrl, {
            headers: {
                Accept: 'application/json',
            },
        });

        if (!response.ok) {
            throw new Error('The form could not be loaded.');
        }

        const { data: form } = await response.json();

        renderForm(form);

        loadingMessage.hidden = true;
        formContainer.hidden = false;
    } catch (error) {
        loadingMessage.hidden = true;
        loadError.textContent = error.message;
        loadError.hidden = false;
    }
}

formElement.addEventListener('submit', async (event) => {
    event.preventDefault();

    clearErrors();
    successMessage.hidden = true;
    submitButton.disabled = true;

    try {
        const response = await fetch(`${formUrl}/submissions`, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                answers: collectAnswers(),
            }),
        });

        const body = await response.json();

        if (response.status === 422) {
            showValidationErrors(body.errors ?? {});
            return;
        }

        if (!response.ok) {
            throw new Error(body.message ?? 'The form could not be submitted.');
        }

        formElement.reset();
        successMessage.textContent = 'Your form was submitted successfully.';
        successMessage.hidden = false;
    } catch (error) {
        submissionErrors.textContent = error.message;
        submissionErrors.hidden = false;
    } finally {
        submitButton.disabled = false;
    }
});

loadForm();