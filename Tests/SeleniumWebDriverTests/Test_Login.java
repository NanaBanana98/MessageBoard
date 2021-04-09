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
 * Tests for the login page.
 * Tests are labeled as such: _ID_Given_Should();
 * When is omitted as all these actions occur on login form submission
 */
public class Test_Login {

    private WebDriver driver;

    //region getters
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
        return driver.findElement(By.name("pass"));
    }

    /**
     * @return the submit button. Used to submit login form
     */
    private WebElement getSubmitBtn(){
        return driver.findElement(By.name("submitted"));
    }

    /**
     * @return The element containing the error message
     */
    private WebElement getErrorMessage(){
        return driver.findElement(By.className("error"));
    }
    //endregion

    @Before
    public void setUp(){
        System.setProperty("webdriver.chrome.driver", "resources/windows/chromedriver.exe");
        driver = new ChromeDriver();
        driver.get(GLOBAL.rootUrl+"mbforum.php");
    }

    @Test
    public void _3_ValidCredentials_ShouldRedirectToForum()
    {
        this.getUsernameInput().sendKeys("AvdoulosA1");
        this.getPasswordInput().sendKeys("17c4badeE");
        this.getSubmitBtn().submit();

        driver.manage().timeouts().implicitlyWait(15, TimeUnit.SECONDS);
        String currentURL = driver.getCurrentUrl();

        Assert.assertEquals(GLOBAL.rootUrl+"mbforum.php",currentURL);
    }

    @Test
    public void _4_InValidCredentials_RedirectToLoginPage()
    {
        this.getUsernameInput().sendKeys("AvdoulosA1");
        this.getPasswordInput().sendKeys("wrong password");
        this.getSubmitBtn().submit();

        driver.manage().timeouts().implicitlyWait(15, TimeUnit.SECONDS);
        String currentURL = driver.getCurrentUrl();

        Assert.assertEquals(GLOBAL.rootUrl+"mbforum.php",currentURL);
    }
    @Test
    public void _11_InvalidCredentials_ShouldDisplayErrorMessage(){
        this.getUsernameInput().sendKeys("wrong username");
        this.getPasswordInput().sendKeys("wrong password");
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
        return driver.findElement(By.xpath("//*[@id=\"login\"]/a[2]"));
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
