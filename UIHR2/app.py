from flask import Flask, render_template
from main import start_face_recognition_process

app = Flask(__name__, template_folder='templates')

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/start_face_recognition', methods=['POST'])
def start_face_recognition():
    # Call the function to start the face recognition process from main.py
    start_face_recognition_process()
    return '', 204  # HTTP 204 No Content response

if __name__ == '__main__':
    app.run(debug=True)
