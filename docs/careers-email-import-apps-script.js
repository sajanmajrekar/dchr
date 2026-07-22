/*
Google Apps Script for importing resumes sent to careers@digichefs.com into the CRM.

Setup:
1. Open script.google.com using the mailbox that receives a copy of careers@digichefs.com emails.
2. Paste this file.
3. Set CRM_WEBHOOK_URL and CRM_IMPORT_TOKEN.
4. Add a time-driven trigger for importCareersResumes, for example every 5 minutes.
*/

const CRM_WEBHOOK_URL = 'https://YOUR-DOMAIN.com/hr/php_actions/import_resume_email.php';
const CRM_IMPORT_TOKEN = 'PASTE_THE_SAME_TOKEN_FROM_inc_local_config_php';
const PROCESSED_LABEL = 'CRM_IMPORTED';
const SEARCH_QUERY = 'to:careers@digichefs.com has:attachment -label:' + PROCESSED_LABEL;
const MAX_THREADS_PER_RUN = 10;

function importCareersResumes() {
  const label = getOrCreateLabel_(PROCESSED_LABEL);
  const threads = GmailApp.search(SEARCH_QUERY, 0, MAX_THREADS_PER_RUN);

  threads.forEach(function(thread) {
    let imported = false;
    const messages = thread.getMessages();
    messages.forEach(function(message) {
      if (messageHasSupportedResume_(message)) {
        postMessageToCrm_(message);
        imported = true;
      }
    });
    if (imported) {
      thread.addLabel(label);
    }
  });
}

function postMessageToCrm_(message) {
  const attachments = message.getAttachments()
    .filter(function(file) {
      return /\.(pdf|doc|docx|rtf|txt)$/i.test(file.getName());
    })
    .map(function(file) {
      return {
        filename: file.getName(),
        mime_type: file.getContentType(),
        data_base64: Utilities.base64Encode(file.getBytes())
      };
    });

  if (!attachments.length) {
    return;
  }

  const payload = {
    token: CRM_IMPORT_TOKEN,
    message_id: message.getId(),
    from_email: extractEmail_(message.getFrom()),
    from_name: extractName_(message.getFrom()),
    subject: message.getSubject(),
    body_text: message.getPlainBody(),
    received_at: message.getDate().toISOString(),
    attachments: attachments
  };

  const response = UrlFetchApp.fetch(CRM_WEBHOOK_URL, {
    method: 'post',
    contentType: 'application/json',
    payload: JSON.stringify(payload),
    muteHttpExceptions: true
  });

  const code = response.getResponseCode();
  if (code < 200 || code >= 300) {
    throw new Error('CRM import failed. HTTP ' + code + ': ' + response.getContentText());
  }
}

function messageHasSupportedResume_(message) {
  return message.getAttachments().some(function(file) {
    return /\.(pdf|doc|docx|rtf|txt)$/i.test(file.getName());
  });
}

function getOrCreateLabel_(name) {
  return GmailApp.getUserLabelByName(name) || GmailApp.createLabel(name);
}

function extractEmail_(value) {
  const match = String(value || '').match(/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i);
  return match ? match[0].toLowerCase() : '';
}

function extractName_(value) {
  return String(value || '')
    .replace(/<[^>]+>/g, '')
    .replace(/"/g, '')
    .trim();
}
