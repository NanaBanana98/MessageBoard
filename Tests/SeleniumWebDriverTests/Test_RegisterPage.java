import org.junit.After;
import org.junit.Assert;
import org.junit.Before;
import org.junit.Test;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.chrome.ChromeDriver;

import java.util.concurrent.TimeUnit;

/**
 * Tests for the register page.
 * Tests are labeled as such: _ID_Given_Should();
 * When is omitted as all these actions occur on register form submission
 */
public class Test_RegisterPage {

    private WebDriver driver;

    //region getters

    /**
     * @return the firstname input field
     */
    private WebElement getFirstNameInput(){
        return driver.findElement(By.name("firstname"));
    }


    /**
     * @return the lastname input field
     */
    private WebElement getLastNameInput(){
        return driver.findElement(By.name("lastname"));
    }

    /**
     * @return the email input field
     */
    private WebElement getEmailInput(){
        return driver.findElement(By.name("email"));
    }

    /**
     * @return the username input field
     */
    private WebElement getUsernameInput(){
        return driver.findElement(By.name("userid"));
    }

    /**
     * @return the password input field
     */
    private WebElement getPasswordInput(){
        return driver.findElement(By.name("password1"));
    }

    /**
     * @return the verify password input
     */
    private WebElement getVerifyPasswordInput(){
        return driver.findElement(By.name("password2"));
    }

    /**
     * @return the submit button for the register form
     */
    private WebElement getSubmitBtn(){
        return driver.findElement(By.name("submitted"));
    }

    /**
     * @return when incorrect credentials are used, an error message should be displayed
     */
    private WebElement getErrorMessage(){
        return driver.findElement(By.className("error"));
    }
    //endregion

    @Before
    public void setUp(){
        System.setProperty("webdriver.chrome.driver", "resources/windows/chromedriver.exe");
        driver = new ChromeDriver();
        driver.get(GLOBAL.rootUrl+"mbregister.php");
    }

    @Test
    public void _1_ValidCredentials_RedirectToForum(){
        this.getFirstNameInput().sendKeys("Jane");
        this.getLastNameInput().sendKeys("Doe");
        this.getEmailInput().sendKeys("fakeEmail@example.io");
        this.getUsernameInput().sendKeys("JaneDoe55");
        this.getPasswordInput().sendKeys("JaneDoe200");
        this.getVerifyPasswordInput().sendKeys("JaneDoe200");
        this.getSubmitBtn().submit();

        driver.manage().timeouts().implicitlyWait(15, TimeUnit.SECONDS);
        String currentURL = driver.getCurrentUrl();

        Assert.assertEquals(GLOBAL.rootUrl+"mbforum.php",currentURL);
    }

    @Test
    public void _2_InvalidCredentials_RedirectToRegister(){
        this.getFirstNameInput().sendKeys("Jane");
        this.getLastNameInput().sendKeys("Doe");
        this.getEmailInput().sendKeys("fakeEmail@example.io");
        this.getUsernameInput().sendKeys("JaneDoe55");
        this.getPasswordInput().sendKeys("JaneDoe200");
        this.getVerifyPasswordInput().sendKeys("eDoe200");
        this.getSubmitBtn().submit();

        driver.manage().timeouts().implicitlyWait(15, TimeUnit.SECONDS);
        String currentURL = driver.getCurrentUrl();

        Assert.assertEquals(GLOBAL.rootUrl+"mbregister.php",currentURL);
    }

    @Test
    public void _10_InvalidCredentials_DisplayErrorMessage(){
        this.getFirstNameInput().sendKeys("Jane");
        this.getLastNameInput().sendKeys("Doe");
        this.getEmailInput().sendKeys("fakeEmail@example.io");
        this.getUsernameInput().sendKeys("JaneDoe55");
        this.getPasswordInput().sendKeys("JaneDoe200");
        this.getVerifyPasswordInput().sendKeys("eDoe200");
        this.getSubmitBtn().submit();

        String message = this.getErrorMessage().getText();
        Assert.assertTrue(message.trim().length() > 0);
    }

    //region Forgot Password
        //region getters

        /**
         * @return the link that redirects to forgot password page
         */
        private WebElement getForgotPasswordBtn(){
            return driver.findElement(By.xpath("//*[@id=\"login\"]/a[3]"));
        }
        //endregion

    @Test
    public void _9_WhenForgotPasswordClicked_RedirectToForgotPasswordPage(){
            this.getForgotPasswordBtn().click();

        driver.manage().timeouts().implicitlyWait(15, TimeUnit.SECONDS);
        String currentURL = driver.getCurrentUrl();

        Assert.assertEquals(GLOBAL.rootUrl+"forgot_password.php",currentURL);

    }
    //endregion

    @After
    public void tearDown(){
        driver.quit();
    }
}
