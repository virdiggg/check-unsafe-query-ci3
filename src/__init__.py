# <root>/src/__init__.py

from datetime import datetime
import logging
import os

class App:
    def __init__(self):
        self.config = {}
        self.unsafe_logger = None
        self.general_logger = None
        self.setup_logging()

    def setup_logging(self):
        log_dir = os.path.join(os.path.dirname(os.path.abspath(__file__)), "..", "logs")
        # Check if the logs directory exists, if not, create it
        if not os.path.exists(log_dir):
            os.mkdir(log_dir)

        # Define log file names with the current date
        today_date = datetime.now().strftime('%Y-%m-%d')
        unsafe_log_file = os.path.join(log_dir, f'unsafe-{today_date}.log')
        general_log_file = os.path.join(log_dir, f'log-{today_date}.log')

        # Configure loggers
        logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')  # Default level

        # Logger for unsafe queries
        self.unsafe_logger = logging.getLogger('unsafe_queries')
        unsafe_handler = logging.FileHandler(unsafe_log_file)
        unsafe_handler.setLevel(logging.WARNING)
        self.unsafe_logger.addHandler(unsafe_handler)

        # Logger for general log
        self.general_logger = logging.getLogger('general_log')
        general_handler = logging.FileHandler(general_log_file)
        general_handler.setLevel(logging.INFO)
        self.general_logger.addHandler(general_handler)

    def set_config(self, key, value):
        self.config[key] = value

    def get_config(self, key):
        return self.config.get(key)

# Create an instance of the App
app = App()
