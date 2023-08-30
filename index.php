
import express, { Request as ExpressRequest, Response as ExpressResponse }  from 'express';
import bodyParser from 'body-parser';
import cookieParser from 'cookie-parser';
import mysql from 'mysql2/promise';
import crypto from 'crypto';
 

class WebClipboard {
  private serverName: string;
  private username: string;
  private password: string;
  private database: string;
  private encryptionPassword: string;
  private connection: mysql.Pool;
  private iv: any;

  constructor() {
    this.serverName = 'your_server_name';
    this.username = 'your_username';
    this.password = 'your_password';
    this.database = 'your_database';
    this.encryptionPassword = 'your_encryption_password';
    this.iv = crypto.randomBytes(16);
    this.connection = mysql.createPool({
        host: this.serverName,
        user: this.username,
        password: this.password,
        database: this.database,
      });
  }

  async start() {
    const app = express();
    app.use(bodyParser.urlencoded({ extended: true }));
    app.use(cookieParser());

    app.post('/', this.handleRequest.bind(this));

    app.listen(3000, () => {
      console.log('Web Clipboard server is running on port 3000');
    });
  }

  private async handleRequest(req: ExpressRequest, res: ExpressResponse) {
    const mode = req.body.mode;
    let out = '';

    if (mode === 'login') {
      this.loginUser(req, res);
    } else if (mode === 'Save Clip') {
      this.saveClip(req, res);
    } else if (mode === 'download') {
      //this.downloadFile(req, res); //doesn't work yet
    } else {
      // Handle other modes or display the login form
      res.send('Not implemented');
    }
  }

  private async loginUser(req: ExpressRequest, res: ExpressResponse) {
    const email = req.body.email;
    const password = req.body.password;
    const cookieName = 'webClipBoard';

    // Check credentials and set the cookie if valid
    const user = await this.getUser(email, password);
    if (user) {
      const encryptedEmail = this.encryptEmail(email);
      res.cookie(cookieName, encryptedEmail, { maxAge: 30 * 365 * 24 * 60 * 60 * 1000 });
      res.redirect('/');
    } else {
      res.send('Invalid credentials');
    }
  }

  private async getUser(email: string, password: string)  {
    const result = await this.connection.query(
      'SELECT * FROM `user` WHERE email = ? AND password = ?',
      [email, password]
    );

    if(result.length > 0 && result[0]) {
        return result[0];
    }
        /*
    if (rows.length > 0) {
      return rows[0];
    }
    */
    return null;
  }

  private encryptEmail(email: string) {
    let cipher: any = crypto.createCipheriv('aes-256-cbc', Buffer.from(this.encryptionPassword), this.iv);
    let encrypted = cipher.update(email);
    encrypted = Buffer.concat([encrypted, cipher.final()]);
    return { iv: this.iv.toString('hex'), encryptedData: encrypted.toString('hex') };
  }

  private async saveClip(req: ExpressRequest, res: ExpressResponse) {
    const userId = req.cookies['webClipBoard'];
    const clip = req.body.clip;
    const dateString: string = new Date().toISOString().slice(0, 19).replace('T', ' ');
    const result = await this.connection.execute(
        "INSERT INTO clipboard_item(user_id, clip, file_name, created) VALUES (?, ?,?,?)",
        [userId, clip, null, dateString]
      );

        
  }

 
}

const webClipboard = new WebClipboard();
webClipboard.start();
