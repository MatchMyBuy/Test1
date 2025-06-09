import os
import uuid
from flask import Flask, request, jsonify, render_template
from google.oauth2 import service_account
from googleapiclient.discovery import build
from googleapiclient.http import MediaFileUpload

# === CONFIG ===
UPLOAD_FOLDER = 'temp_uploads'
GDRIVE_FOLDER_ID = '11IO1jT4buWeh5xTqw0CIJNEUU030w4uA'  # Your actual Drive folder ID

app = Flask(__name__)
app.config['UPLOAD_FOLDER'] = UPLOAD_FOLDER
os.makedirs(UPLOAD_FOLDER, exist_ok=True)

# === GOOGLE AUTH ===
SCOPES = ['https://www.googleapis.com/auth/drive.file']
SERVICE_ACCOUNT_FILE = 'capstone-461808-728093f90e04.json'
credentials = service_account.Credentials.from_service_account_file(
    SERVICE_ACCOUNT_FILE, scopes=SCOPES)
drive_service = build('drive', 'v3', credentials=credentials)

# === ROUTES ===
@app.route('/')
def index():
    return render_template('upload.html')  # render your UI

@app.route('/api/upload_audio', methods=['POST'])
def upload_audio():
    print("📥 Received upload request")

    try:
        if 'audio_file' not in request.files:
            return jsonify({'success': False, 'message': 'No audio_file in request'}), 400

        audio_file = request.files['audio_file']
        filename = f"{uuid.uuid4()}_{audio_file.filename}"  # Unique filename
        filepath = os.path.join(app.config['UPLOAD_FOLDER'], filename)
        audio_file.save(filepath)

        # Upload to Google Drive
        media = MediaFileUpload(filepath, mimetype='audio/mpeg')
        file_metadata = {'name': filename, 'parents': [GDRIVE_FOLDER_ID]}
        uploaded_file = drive_service.files().create(
            body=file_metadata, media_body=media, fields='id, name'
        ).execute()

        # Explicitly close the MediaFileUpload to release the file handle
        media.stream().close()

        # Now that it's closed, safely delete the file
        os.remove(filepath)

        return jsonify({'success': True, 'file_id': uploaded_file['id'], 'file_name': uploaded_file['name']})

    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True)