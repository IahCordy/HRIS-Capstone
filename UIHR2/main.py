import cv2
import face_recognition
import os
import tkinter as tk
from tkinter import simpledialog
from PIL import Image, ImageTk
import time
import mysql.connector
import numpy as np

def start_face_recognition_process():
    def open_webcam():
        for i in range(10):  # Try up to 10 webcam indices
            video_capture = cv2.VideoCapture(i)
            if video_capture.isOpened():
                print(f"Webcam found at index {i}")
                return video_capture
            else:
                print(f"No webcam found at index {i}")
        return None

    # Open webcam
    video_capture = open_webcam()
    if video_capture is None:
        print("Failed to open any webcam.")
        exit()

    # Initialize MySQL connection
    # Replace 'YOUR_PASSWORD' with your actual MySQL password
    db_connection = mysql.connector.connect(
        host="localhost",
        user="root",
        password='',
        database="facerecog",
        port=3307
    )
    db_cursor = db_connection.cursor()

    # Initialize empty lists for known face encodings and names
    known_face_encodings = []
    known_face_names = []

    # Load known faces from the "storing" table in the database
    db_cursor.execute("SELECT name, face_encoding FROM storing")
    for name, face_encoding_blob in db_cursor.fetchall():
        # Convert the face encoding blob to a numpy array
        face_encoding = np.frombuffer(face_encoding_blob, dtype=np.float64)
        known_face_encodings.append(face_encoding)
        known_face_names.append(name)

    # Function to register a new face
    def register_face():
        ret, frame = video_capture.read()
        name = simpledialog.askstring("Register Face", "Enter your name for registration:")
        if name:
            face_locations = face_recognition.face_locations(frame)
            if not face_locations:
                print("No face found for registration.")
                return None

            # Use the first face found for registration
            face_encoding = face_recognition.face_encodings(frame, face_locations)[0]

            # Convert the face encoding to bytes before storing in the database
            face_encoding_bytes = face_encoding.tobytes()

            # Save the registered face encoding in the "storing" table in the database
            try:
                db_cursor.execute("INSERT INTO storing (name, face_encoding) VALUES (%s, %s)", (name, face_encoding_bytes))
                db_connection.commit()
            except mysql.connector.Error as e:
                print(f"Error registering face: {e}")

    # Function to record timein
    def record_timein():
        record_attendance("timein")

    # Function to record timeout
    def record_timeout():
        record_attendance("timeout")

    # Function to record attendance
    # Function to record attendance
    def record_attendance(record_type):
        try:
            ret, frame = video_capture.read()
            face_locations = face_recognition.face_locations(frame)
            face_encodings = face_recognition.face_encodings(frame, face_locations)

            print("Number of faces detected:", len(face_locations))  # Add this line for debugging

            for (top, right, bottom, left), face_encoding in zip(face_locations, face_encodings):
                best_match_name = "Unknown"

                for known_name, known_encoding in zip(known_face_names, known_face_encodings):
                    # Compare the detected face encoding with each known face encoding
                    is_match = face_recognition.compare_faces([known_encoding], face_encoding, tolerance=0.4)[0]  # Decrease tolerance for stricter matching

                    # If there's a match, update the best match
                    if is_match:
                        best_match_name = known_name
                        break  # Stop further comparisons once a match is found

                # Record time now for the employee
                time_now = time.strftime('%I:%M:%S %p')  # 12-hour format with AM or PM
                date = time.strftime('%Y-%m-%d')

                # Only store the attendance if the face is recognized as known
                if best_match_name != "Unknown":
                    try:
                        if record_type == "timein":
                            # Check if there's an existing record for the employee and date in timein table
                            db_cursor.execute("SELECT id FROM timein WHERE name = %s AND date = %s", (best_match_name, date))
                            existing_record = db_cursor.fetchone()

                            if not existing_record:
                                # If there's no existing record, insert a new record with timein
                                db_cursor.execute("INSERT INTO timein (name, timein, date) VALUES (%s, %s, %s)", (best_match_name, time_now, date))
                        elif record_type == "timeout":
                            # Check if there's an existing record for the employee and date in timeout table
                            db_cursor.execute("SELECT id FROM timeout WHERE name = %s AND date = %s", (best_match_name, date))
                            existing_record = db_cursor.fetchone()

                            if not existing_record:
                                # If there's no existing record, insert a new record with timeout
                                db_cursor.execute("INSERT INTO timeout (name, timeout, date) VALUES (%s, %s, %s)", (best_match_name, time_now, date))

                        db_connection.commit()
                    except mysql.connector.Error as e:
                        print(f"Error recording {record_type}: {e}")

                # Draw rectangle and display name on the camera feed
                cv2.rectangle(frame, (left, top), (right, bottom), (0, 255, 0), 2)
                font = cv2.FONT_HERSHEY_DUPLEX
                text = f"Name: {best_match_name}"  # Display only the name
                cv2.putText(frame, text, (left + 6, bottom + 20), font, 0.5, (255, 255, 255), 1)

                # Display the resulting frame in the tkinter window
                cv2image = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
                pil_image = Image.fromarray(cv2image)
                tk_image = ImageTk.PhotoImage(image=pil_image)

                label.configure(image=tk_image)
                label.image = tk_image

                # Update the tkinter window
                root.update()

                # Introduce a delay (adjust the time as needed)
                time.sleep(1)

            print(f"{record_type.capitalize()} recorded")  # Add this line for debugging

        except Exception as e:
            print(f"Error in face recognition: {e}")


    # Create a simple tkinter window
    root = tk.Tk()
    root.title("Face Recognition")

    # Create a label for displaying the video feed
    label = tk.Label(root)
    label.pack()

    # Create buttons for face registration, timein, and timeout
    register_button = tk.Button(root, text="Register Face", command=register_face)
    register_button.pack()

    timein_button = tk.Button(root, text="Time In", command=record_timein)
    timein_button.pack()

    timeout_button = tk.Button(root, text="Time Out", command=record_timeout)
    timeout_button.pack()

    # Function to handle keyboard events
    def on_key_press(event):
        if event.char == "q":
            root.destroy()

    # Bind the keyboard events to the function
    root.bind("<Key>", on_key_press)

    # Main loop to display video feed
    while True:
        # Capture frame-by-frame
        ret, frame = video_capture.read()

        # Display the resulting frame in the tkinter window
        cv2image = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
        pil_image = Image.fromarray(cv2image)
        tk_image = ImageTk.PhotoImage(image=pil_image)

        label.configure(image=tk_image)
        label.image = tk_image

        # Update the tkinter window
        root.update()

        # Check for key press to exit loop and release/close resources
        if cv2.waitKey(1) & 0xFF == ord('q'):
            break

    # Release webcam and close windows
    if video_capture is not None:
        video_capture.release()
        cv2.destroyAllWindows()


if __name__ == '__main__':
    start_face_recognition_process()
